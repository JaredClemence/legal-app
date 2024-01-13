<?php

namespace App\Http\Controllers\KCBA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KCBA\Member as BarMember;
use App\Models\User;
use App\Models\KCBA\WorkEmail;
use App\Models\KCBA\Firm;
use App\Events\KCBA\AdminCreatedMembers;
use App\Http\Controllers\KCBA\Components\BulkMemberCreator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $member = $this->getMember($request->user());
        $firm_id = $member->firm_id;
        $members = BarMember::with(['user'])->where('firm_id', '=', $firm_id)->get();
        return view('kcba.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->createOrUpdateUser($request);
        $firm = $this->createOrUpdateFirm($request);
        $member = $this->createOrUpdateMember($request, $user, $firm);
        $this->announceAdminCreatedMembers($request, collect([$member]));
        $members = [];
        return view('kcba.members.index', compact('user','firm','member', 'members'));
    }
    
    /**
     * Receive form data for multiple member creations.
     */
    public function createBulk(Request $request, BulkMemberCreator $helper)
    {
        $activeMember = BarMember::where('user_id','=',$request->user()?->id)->get()->first();
        //dd([$activeMember?->isAdmin() === false,$activeMember === null ]);
        if( $activeMember === null || $activeMember?->isAdmin() === false){
            $responseCode = $request->user() ? 403 : 401;
            return response('unauthorized access', $responseCode);
        }
        try{
            $helper->process($request);
            $members = $helper->getNewMembers();
            $this->announceAdminCreatedMembers($request, $members);
            $members = [];
            return view('kcba.members.index', compact('members'));
        }catch( Exception $e ){
            return response( $e->getMessage(), $e->getCode() );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BarMember $member)
    {
        //
    }
    
    public function showBulkForm(Request $request){
        return view('kcba.members.bulk');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, BarMember $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarMember $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, BarMember $member)
    {
        //
    }

    public function getMember(User $user) : BarMember {
        return BarMember::where('user_id','=',$user->id)->get()->first();
    }

    protected function createOrUpdateUser(Request $request) : User {
        $user = User::where('email','=',$request->input('email'))->get()->first();
        if( $user === null ){
            $user = User::create( [
                'name'=> $request->input('name'),
                'email'=> $request->input('email'),
                'password'=> fake()->password
            ] );
        }
        else if ($user->name !== $request->input('name') ){
            $user->name = $request->input('name');
        }
        if( $user->isDirty() ){
            $user->save();
            $user->refresh();
        }
        return $user;
    }

    protected function createOrUpdateFirm(Request $request) {
        if($request->input('firm_name',null)==null) return null;
        $firm = Firm::firstOrCreate(
                [
                    'firm_name'=>$request->input('firm_name')
                ]
                );
        return $firm;
    }

    protected function createOrUpdateMember(Request $request, User $user, Firm $firm=null) : BarMember {
        $member = BarMember::where('work_email','=',$request->input('work_email', $user->email))->get()->first();
        if( $member === null ){
            $member = BarMember::create(
                    [
                        'user_id'=>$user->id,
                        'firm_id'=>$firm?->id,
                        'work_email'=>$request->input('work_email', $user->email),
                        'barnum'=>$request->input('barnum',''),
                        'status'=>'PENDING'
                        ]
                    );
        }else{
            if( $member->user_id !== $user->id ) $member->user_id = $user->id;
            if( $member->firm_id !== $firm?->id ) $member->firm_id = $firm?->id;
            if( $member->barnum !== $request->input('barnum','') ) $member->barnum = $request->input('barnum','');
            if( $member->work_email !== $request->input('work_email','') ) $member->work_email = $request->input('work_email','');
            if( $member->isDirty() ){
                $member->save();
                $member->refresh();
            }
        }
        return $member;
    }

    private function announceAdminCreatedMembers(Request $request, $memberCollection) {
        $user =  $request->user();
        $userid = $user?->id;
        if( $userid ){
            $member = BarMember::where('user_id','=',$userid)->first();
            $isAdmin = $member?->isAdmin();
            
            if( $isAdmin ){
                AdminCreatedMembers::dispatch($memberCollection);
            }
        }
    }

    

}
