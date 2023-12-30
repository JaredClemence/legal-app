<?php

namespace App\Http\Controllers;

use App\Models\Pcp\Member;
use Illuminate\Http\Request;
use App\Models\KCBA\WorkEmail;
use App\Models\KCBA\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $firm_name = $request->user()->work_email->firm_name;
        $emails = WorkEmail::with(['member'])->where(['firm_name'=>$firm_name])->get();
        $members = $emails->map(
                function( $email ){
            $member = $email->member;
            $member->work_email = $email;
            return $member;
                }
                );
        return view('kcba.members.index', compact('memeber'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }
}
