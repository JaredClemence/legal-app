<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @var string paypal_id [1..36]
 * @var string status [1..20]
 * @var string intent
 * @var json paypal_order_obj
 * @var json payment_source
 * @var json payments
 * @var json payer
 * @var json links
 */
class Order extends Model
{
    use HasFactory;
    
    protected $fillable = ['paypal_id'];
}
