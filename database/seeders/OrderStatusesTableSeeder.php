<?php
namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OrderStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	OrderStatus::query()->delete();

    	$orderStatuses = [
    		[
                'name'  => 'Pending',
                'notification'  => 1,
    		],
            [
                'name'  => 'Processed',
                'notification'  => 1,
            ],
            [
                'name'  => 'Delivered',
                'notification'  => 1,
            ],
            [
                'name'  => 'Done',
                'notification'  => 0,
            ],
    	];

    	OrderStatus::insert($orderStatuses);
    }
}
