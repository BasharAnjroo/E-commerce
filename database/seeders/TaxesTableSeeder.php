<?php
namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;
class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Tax::query()->delete();
    	$taxes = [
    		[
				'name'  => 'VAT',
				'value' => '10.000000',
    		],
    	];

    	Tax::insert($taxes);
    }
}
