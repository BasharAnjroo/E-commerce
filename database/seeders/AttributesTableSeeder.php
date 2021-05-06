<?php
namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Attribute::query()->delete();

    	$attributes = [
    		[
				'id'   => 1,
				'type' => 'dropdown',
				'name' => 'Color',
    		],
    		[
				'id'   => 2,
				'type' => 'dropdown',
				'name' => 'Size',
    		]
    	];

    	Attribute::insert($attributes);
    }
}
