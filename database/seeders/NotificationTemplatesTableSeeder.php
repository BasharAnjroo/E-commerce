<?php
namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NotificationTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	NotificationTemplate::query()->delete();

    	$notificationTemplates = [
    		[
                'name'  => 'Order Status Changed',
                'slug'  => 'order-status-changed',
                'model' => 'Order',
                'body'  => '<p>Hello,&nbsp;&nbsp;{{ userName }},</p>
                            <p>Your order status was changed to&nbsp;&nbsp;{{ status }}.</p>

                            <p>Best,</p>
                            <p>eStarter team.</p>',
    		],
    	];

    	NotificationTemplate::insert($notificationTemplates);
    }
}
