<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{


    /*
	|--------------------------------------------------------------------------
	| GLOBAL VARIABLES
	|--------------------------------------------------------------------------
	*/

    protected $table = 'products';
    //protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
    	'group_id',
    	'attribute_set_id',
        'attribute_set',
    	'name',
    	'description',
    	'price',
    	'tax_id',
    	'sku',
    	'stock',
    	'active',
        'city_id',
        'City',
        'address',
    	'created_at',
    	'updated_at'
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
            $model->categories()->detach();
            $model->attributes()->detach();

            // Delete product images
            $disk = 'uploads';

            foreach ($model->images as $image) {
                // Delete image from disk

                    Storage::disk($disk)->delete($image->name);


                // Delete image from db
                $image->delete();
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

	public function categories()
	{
		return $this->belongsToMany(Category::class);
	}

	public function attributes()
	{
		return $this->belongsToMany(Attribute::class, 'attribute_product_value', 'product_id', 'attribute_id')->withPivot('value');
	}
	public function city()
	{
		return $this->hasOne(City::class, 'id');
	}
	public function tax()
	{
		return $this->hasOne(Tax::class, 'id','tax_id');
	}

	public function images()
	{
		return $this->hasMany(ProductImage::class)->orderBy('order', 'ASC');
	}

    public function group()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function cartRules()
    {
        return $this->belongsToMany('Tax::classApp\Models\CartRule');
    }

    public function specificPrice()
    {
        return $this->belongsTo(SpecificPrice::class,'id','product_id');
    }



    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeLoadCloneRelations($query)
    {
        $query->with('categories', 'attributes', 'images');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }


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
