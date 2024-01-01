<?php

namespace App\Http\Controllers\KCBA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KCBA\Member as BarMember;
use App\Models\User;
use App\Models\KCBA\WorkEmail;

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
        //
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

}
