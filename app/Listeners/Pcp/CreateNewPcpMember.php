<?php

namespace App\Listeners\Pcp;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Pcp\NewMemberPaymentCollected;
use App\Http\Controllers\Paypal\Classes\Payee;
use App\Models\Pcp\Member;
use App\Events\Pcp\NewPcpMemberCreated;

class CreateNewPcpMember
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewMemberPaymentCollected $event): void
    {
        try{
            $member = $this->buildMemberFromOrderPayer($event->order->payer);
        }catch(\Exception $e){
            return false; //stop other event propagation
        }
        $member->save();
        NewPcpMemberCreated::dispatch( $member );
    }

    public function buildMemberFromOrderPayer($payer) {
        /** @var Payer $payer */
        $email = $payer->email_address;
        $member = Member::firstOrNew('email',$email);
        if($member->id){
            throw new \Exception("Member existed for id alread.");
        }
        $member->name = $payer->name->surname . ", " . $payer->name->given_name;
        $member->email = $email;
        if( $payer->address ){
            $member->address_line_1 = $payer->address_line_1 ?? null;
            $member->address_line_2 = $payer->address_line_2 ?? null;
            $member->city = $payer->admin_area_2 ?? null;
            $member->state = $payer->admin_area_1 ?? null;
            $member->postal_code = $payer->postal_code ?? null;
            $member->country_code = $payer->country_code ?? null;
        }
        if( $payer->phone_number ){
            $member->phone_number = $member->phone_number->phone_number ?? null;
        }
        return $member;
    }

}
