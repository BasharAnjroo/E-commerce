<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Category::query()->delete();

    	$categories = [
    		[
				'parent_id' => null,
				'name'      => 'Category Example',
				'slug'      => 'cat-example',
				'lft'       => 2,
				'rgt'       => 5,
				'depth'     => 1,
    		],
    		[
				'parent_id' => 1,
				'name'      => 'Subcategory Example',
				'slug'      => 'subcat-example',
				'lft'       => 3,
				'rgt'       => 4,
				'depth'     => 2,
    		]
    	];

    	Category::insert($categories);
    }
}
