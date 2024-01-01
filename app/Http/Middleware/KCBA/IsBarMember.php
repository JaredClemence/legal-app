<?php

namespace App\Http\Middleware\KCBA;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KCBA\User;
use App\Models\KCBA\Member;

class IsBarMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isSectionMember = $this->isSectionMember( $request->user() );
        if( $isSectionMember ){
            return $next($request);
        }else{
            return redirect('bar.login');
        }
    }

    public function isSectionMember($user) {
        if( $user == null ) return false;
        
        $id = $user->id;
        $member = Member::where('user_id','=',$id)->get();
        return $member !== null;
    }

}
