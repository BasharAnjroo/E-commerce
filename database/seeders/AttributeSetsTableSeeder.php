<?php
namespace Database\Seeders;

use App\Models\AttributeSet;
use Illuminate\Database\Seeder;

class AttributeSetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	AttributeSet::query()->delete();

    	$attributeSets = [
    		[
				'name' => 'Clothes',
    		],
    	];

    	AttributeSet::insert($attributeSets);
    }
}
