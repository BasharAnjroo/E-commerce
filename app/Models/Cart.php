<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['id','content','key','user_id'];
    public $incrementing = false;

    public function items () {
        return $this->hasMany(CartItem::class, 'Cart_id');
    }


}
