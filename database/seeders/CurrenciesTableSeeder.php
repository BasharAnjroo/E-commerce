<?php
namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Currency::query()->delete();

    	$currencies = [
    		[
                'name'  => 'Euro',
                'iso'  => 'EUR',
                'value' => '1',
				'default'  => '1',
    		],
    	];

    	Currency::insert($currencies);
    }
}
