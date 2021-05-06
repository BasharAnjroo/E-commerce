<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Attribute extends Model
{
    use HasApiTokens,Notifiable;

 	/*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    protected $table = 'attributes';
    //protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
    	'type',
	 	'name'
 	];
    // protected $hidden = [];
    // protected $dates = [];

    /*
	|--------------------------------------------------------------------------
	| EVENTS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
    {
        parent::boot();

    	static::deleting(function($model) {
	        if (count($model->sets) == 0) {
    	        $model->values()->delete();
    		} else {
    			return $model;
    		}
        });
    }

    /*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/

    /*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function sets()
    {
    	return $this->belongsToMany(AttributeSet::class, 'attribute_attribute_set', 'attribute_id', 'attribute_set_id');
    }
    public function Product()
	{
		return $this->belongsToMany('App\Models\Product', 'attribute_product_value', 'product_id', 'attribute_id')->withPivot('value');
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
