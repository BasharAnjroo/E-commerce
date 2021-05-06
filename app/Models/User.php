<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use \DateTimeInterface;
use Illuminate\Support\Facades\Hash as Hash;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'address',
        'gender',
        'birthday',
        'country',
        'city',
        'zip-code',
        'p_image',
        'verified',
        'active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
protected $casts = [
        'email_verified_at' => 'datetime',
    ];

protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }
public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
public function gyms()
    {
        return $this->belongsToMany(Gym::class);
    }
public function subscriptions()
    {
        return $this->belongsToMany(Subscriptions::class);
    }
public function advertisements()
    {
        return $this->belongsToMany(advertisement::class);
    }
    public function isadmin($role_id)
    {   if ($role_id == 1)
        return true;
    else
        return false;
    }
    public function AauthAcessToken(){
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function verifyUser()
{
  return $this->hasOne(VerifyUser::class);
}
}
