<?php

namespace App\Models;

use App\Mail\NotificationTemplateMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Order extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';
    //protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'products',
        'user_id',
        'domain',
        'status_id',
        'status',
        'carrier_id',
        'shipping_name',
        'currency_id',
        'currency',
        'exchange_rate',
        'comment',
        'shipping_no',
        'invoice_no',
        'invoice_date',
        'delivery_date',
        'payment_status',
        'subtotal',
        'total_discount',
        'tax_id',
        'total_tax',
        'total_shipping',
        'total',
        'name_sender',
        'first_name_receiver',
        'last_name_receiver',
        'email',
        'address1',
        'phone',
        'country_id',
        'country',
        'payment_method',
        'user_agent',
        'ip',
        'transaction',

    ];
    // protected $hidden = [];
    // protected $dates = [];
    public $notificationVars = [
        'userSalutation',
        'name_sender',
        'email',
        'shipping_name',
        'total',
        'status'
    ];

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS VARIABLES
    |--------------------------------------------------------------------------
    */
    public function notificationVariables()
    {
        return [
            'userSalutation' => $this->user->salutation,
            'name_sender'       => $this->user->name,
            'email'      => $this->user->email,
            'total'          => $this->total(),
            'shipping_name'        => $this->carrier()->first()->name,
            'status'         => $this->orderStatus->name
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */
     protected static function boot()
    {
        parent::boot();

 /*        static::updating(function($order) {
            // Send notification when order status was changed
            $oldStatus = $order->getOriginal();
            if ($order->status_id != $oldStatus['status_id'] && $order->orderStatus->notification != 0) {
                // example of usage: (be sure that a notification template mail with the slug "example-slug" exists in db)
                return Mail::to($order->user->email)->send(new NotificationTemplateMail($order, "order-status-changed"));
            }
        }); */
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function total()
    {
        return decimalFormat($this->products->sum(function ($product) {
            return $product->pivot->price_with_tax * $product->pivot->quantity;
        }, 0) + $this->carrier->price);
    }
    public static function getOrderAdmin($id) {
        return self::with(['details', 'orderTotal'])
        ->where('id', $id)
        ->first();
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
    public function orderTotal()
    {
        return $this->hasMany(OrderTotal::class, 'order_id', 'id');
    }
    public function orderStatus()
    {
        return $this->hasOne(OrderStatus::class, 'id', 'status_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\OrderStatus', 'id', 'status_id');
    }

    public function statusHistory()
    {
        return $this->hasMany('App\Models\OrderStatusHistory')->orderBy('created_at', 'DESC');
    }

    public function carrier()
    {
        return $this->hasOne('App\Models\Carrier', 'id', 'carrier_id');
    }

    public function shippingAddress()
    {
        return $this->hasOne('App\Models\Address', 'id', 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->hasOne('App\Models\Address', 'id', 'billing_address_id');
    }

    public function billingCompanyInfo()
    {
        return $this->hasOne('App\Models\Company', 'id', 'billing_company_id');
    }

    public function currency()
    {
        return $this->hasOne('App\Models\Currency', 'id', 'currency_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')->withPivot(['name', 'sku', 'price', 'tax',  'quantity']);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
