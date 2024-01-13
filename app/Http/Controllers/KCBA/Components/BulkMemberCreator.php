<?php

namespace App\Http\Controllers\KCBA\Components;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\KCBA\Member;

class BulkMemberCreator extends Controller
{
    private $members = null;
    
    public function process(Request $request){
        try{
            DB::beginTransaction();
            $this->createBulkMembersFromRequestData($request);
            DB::commit();
        }catch( \Exception $e ){
            DB::rollBack();
            throw $e;
        }
    }
    public function getNewMembers() : Collection {
        return $this->members;
    }
    private function createBulkMembersFromRequestData(Request $request){
        try{
            $users = $this->createBulkUsers($request);
            $firms = $this->createBulkFirms($request);
            $members = $this->createBulkMembers($request, $users, $firms);
            $this->members = $members;
        }catch(\Exception $e){
            if( $e->getCode()!=982 ){
                //Log error but don't crash.
                //most likely cause is badly labeled fields.
                throw $e;
            }
        }
    }
    private function createBulkUsers(Request $request) {
        $userRows = [];
        for( $i=0; $i<100; $i++){
            $user = $this->extractUserRow($request, $i);
            if( $user['email'] == '' ) break;
            $userRows[] = $user;
        }
        $goodUsers = collect( $userRows )->filter(function($user){
            return $user['email']!=='';
        });
        if( $goodUsers->count() === 0 ){
            throw new \Exception( "User rows is empty.",982);
        }
        DB::table('users')->insertOrIgnore($goodUsers->toArray());
        $emails = $goodUsers
                ->map(function($user){return $user['email'];})
                ->filter(function($email){
                    return $email != null;
                });
        $users = DB::table('users')->select('id','email')->whereIn('email',$emails)->get();
        return $users;
    }

    private function createBulkFirms(Request $request) {
        $firmRows = [];
        for( $i=0; $i<100; $i++){
            $firm = $this->extractFirmRow($request, $i);
            if( $firm === null ){
                break;
            }else{
                $firmRows[] = $firm;
            }
        }
        $firmRowsReduced = $this->reduceFirmRowsDuplicates($firmRows);
        DB::table('firms')->insertOrIgnore($firmRowsReduced->toArray());
        $firms = DB::table('firms')
                ->select('id','firm_name')
                ->whereIn('firm_name',$firmRowsReduced)
                ->get();
        return $firms;
    }

    private function createBulkMembers(Request $request, $users, $firms) {
        $memberRows = [];
        for( $i=0; $i<100; $i++){
            $user = $this->extractMemberRow($request, $i, $users, $firms);
            if( $user == null || $user['work_email']=="" ) break;
            $memberRows[] = $user;
        }
        DB::table('members')->insertOrIgnore($memberRows);
        
        $memberCollection = collect($memberRows);
        $emailCollection = $memberCollection->map(
                    function($member){
                        return $member["work_email"];
                    }
                );
        $uniqueEmails = $emailCollection->unique();
        
        $members = Member::whereIn('work_email',$uniqueEmails)
                ->with(['user','firm'])
                ->get();
        return $members;
    }

    private function extractUserRow(Request $request, $i) {
        $row = [
            'name'=>$request->input('name_' . $i, ''),
            'email'=>$request->input('email_' . $i, ''),
            'email_verified_at'=>null,
            'password'=>fake()->password
        ];
        return $row;
    }

    private function extractFirmRow(Request $request, $i) {
        $row = [
            'firm_name'=> $request->input('firm_' . $i, '')
        ];
        return $row;
    }

    private function extractMemberRow(Request $request, $i, $users, $firms) {
        $email = $request->input('email_' . $i,"");
        if($email == "" ) return null;
        $userid = $this->findUserId( $users, $email );
        
        $firm_name = $request->input('firm_' . $i, '');
        $firmid = $this->findFirmId( $firms, $firm_name );
        
        $row = [
            'user_id'=>$userid,
            'firm_id'=>$firmid,
            'work_email'=>$email,
            'barnum'=>$request->input('barnum_' . $i, ''),
            'status'=>$request->input('status_' . $i, 'PENDING'),
            'role'=>$request->input('role_' . $i, 'USER')
        ];
        return $row;
    }
    
    private function findUserId( $userCollection, $email ){
        return $userCollection
                ->filter(
                        function($user) use ($email) {return $user->email==$email;}
                        )
                ->first()?->id;
    }
    
    private function findFirmId( $firmCollection, $firm_name ){
        return $firmCollection
                ->filter(
                        function($firm) use ($firm_name) {return $firm->firm_name==$firm_name;}
                        )
                ->first()?->id;
    }

    private function reduceFirmRowsDuplicates($firmRows) {
        return collect($firmRows)->unique();
    }
}
