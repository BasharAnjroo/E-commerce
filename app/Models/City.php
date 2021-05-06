<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class City extends Model
{
    use HasFactory;
    use HasApiTokens,Notifiable;

    protected $table = 'city';
    //protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = [
    	'name',
    	'status',
        'created_at',
        'updated_at',
        'deleted_at',
	];
}
