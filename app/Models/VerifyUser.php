<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class VerifyUser extends Model
{
    use HasFactory;
    use HasApiTokens,Notifiable;
    protected $fillable = ['user_id','token'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
