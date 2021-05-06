<?php
namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CarriersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Carrier::query()->delete();

    	$carriers = [
    		[
                'name'  => 'Best Express',
                'price'  => '20',
                'delivery_text' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
				'logo'  => null,
    		],
    	];
    	Carrier::insert($carriers);
    }
}
