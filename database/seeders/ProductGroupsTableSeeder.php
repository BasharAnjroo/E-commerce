<?php
namespace Database\Seeders;

use App\Models\ProductGroup;
use Illuminate\Database\Seeder;

class ProductGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	ProductGroup::query()->delete();

    	$productGroups = [
    		[
				'id'   => 1,
    		],
    	];

    	ProductGroup::insert($productGroups);
    }
}
