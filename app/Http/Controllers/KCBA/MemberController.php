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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $formData = $member->getFormData();
        return view('kcba.members.edit', $formData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BarMember $member)
    {
        $changedFields = $this->determineChanges($request, $member);
        $loggedInMember = $this->getAuthenticatedMember();
        if( count( $changedFields ) > 0 ){
            //at least one change has been made
            if( $loggedInMember->isAdmin() ){
                //admin
                $this->applyChangesAsAdmin( $changedFields, $member );
            }else if( $this->hasValidToken($request) ){
                //token bearer
                $this->applyChangesAsTokenBearer( $changedFields, $member );
            }else if( Auth::user()?->id == $member->user->id ){
                //just a regular user
                $this->applyChangesAsSelf($changedFields, $member);
            }else{
                return response("Invalid access to other user's data.", 403);
            }
        }
        return $this->edit($request, $member);
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

    private function determineChanges(Request $request, BarMember $member) {
        $changedData = [];
        $currentValues = $member->getFormData();
        $requestInputs = $request->all();
        foreach($currentValues as $key=>$data){
            if( isset($requestInputs[$key]) && $data != $requestInputs[$key] ){
                $changedData[$key]=$requestInputs[$key];
            }
        }
        return $changedData;
    }

    private function applyChangesAsAdmin($changedFields, $member) {
        foreach($changedFields as $key=>$value){
            switch($key){
            }
        }
    }

    private function hasValidToken($request) {
        
    }

    private function applyChangesAsTokenBearer($changedFields, $member) {
        foreach($changedFields as $key=>$value){
            switch($key){
                case 'barnum':
                    break;
                case 'name':
                    break;
                case 'password':
                    break;
                case 'firm_name':
                    break;
            }
        }
    }

    private function applyChangesAsSelf($changedFields, $member) {
        $user = $member->user;
        $firm = $member->firm;
        foreach($changedFields as $key=>$value){
            switch($key){
                case 'barnum':
                    $member->barnum = $value;
                    break;
                case 'name':
                    $user->name = $value;
                    break;
                case 'password':
                    $user->password = Hash::make($value);
                    break;
                case 'firm_name':
                    if($firm->firm_name != $value){
                        //new firm needs to be selected
                        $this->setFirmToNewValue($member, $value);
                    }
                    break;
            }
        }
        if( $user->isDirty() ){
            $user->save();
        }
        if( $member->isDirty() ){
            $member->save();
        }
    }

    private function getAuthenticatedMember() {
        $user = Auth::user();
        $member = null;
        if($user){
            $member = BarMember::where('user_id','=',$user->id)->first();
        }
        return $member;
    }

    private function setFirmToNewValue(BarMember $member, string $value) {
        $newFirm = Firm::where('firm_name','=',$value)->first();
        if( $newFirm == null ){
            $newFirm = Firm::factory()->create(['firm_name'=>$value]);
        }
        $member->firm_id = $newFirm->id;
    }

}
