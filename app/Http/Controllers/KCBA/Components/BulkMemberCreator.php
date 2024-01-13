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
        $users = $this->createBulkUsers($request);
        $firms = $this->createBulkFirms($request);
        $members = $this->createBulkMembers($request, $users, $firms);
        $this->members = $members;
    }
    private function createBulkUsers(Request $request) {
        $userRows = [];
        for( $i=0; $i<100; $i++){
            $user = $this->extractUserRow($request, $i);
            if( $user === null ){
                break;
            }else{
                $userRow[] = $user;
            }
        }
        DB::table('users')->insertOrIgnore($userRows);
        $emails = collect($userRows)
                ->select('id','email')
                ->whereIn('email',$emails)
                ->get();
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
        DB::table('firms')->insertOrIgnore($firmRowsReduced);
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
            if( $user === null ){
                break;
            }else{
                $userRow[] = $user;
            }
        }
        DB::table('users')->insertOrIgnore($userRows);
        $emails = collect($userRows)->map(function($member){
            return $member['work_email'];
        })->unique();
        $members = Member::whereIn('work_email',$emails)
                ->with(['user','firm'])
                ->get();
        return $members;
    }

    private function extractUserRow(Request $request, $i) {
        $row = [
            'name'=>$request->input('name_' . $i, ''),
            'email'=>$request->input('email_' . $i),
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
        $email = $request->input('email_' . $i);
        $firm_name = $request->input('firm_' . $i, '');
        $userFinder = function( $email ){
            return function( $userRow ) use ($email){
                return ($userRow['email']==$email);
            };
        };
        $firmFinder = function( $firmName ){
            return function( $userRow ) use ($firmName){
                return ($userRow['firm_name']==$firmName);
            };
        };
        $row = [
            'user_id'=>$users->first( $userFinder($email) )->select('id'),
            'firm_id'=>$firms->first( $firmFinder($firm_name) )->select('id'),
            'work_email'=>$email,
            'barnum'=>$request->input('barnum_' . $i, ''),
            'status'=>$request->input('status_' . $i, 'PENDING'),
            'role'=>$request->input('role_' . $i, 'USER')
        ];
        return $row;
    }

    private function reduceFirmRowsDuplicates($firmRows) {
        return collect($firmRows)->unique();
    }
}
