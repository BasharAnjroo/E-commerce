<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'                  => 1,
                'name'                => 'Admin',
                'email'               => 'admin@admin.com',
                'password'            => '$2y$10$XH232IuTQCcPEkbswdv4UeGvuvnj26M1HLKkXtN4Dgx5gyI1WCIMi',
                'phone'               => '0221311',
                'is-sub'              =>true,
                'address'             => 'مشروع القلعة',
                'p_image'             => null,

                'remember_token'      => null,
            ],
            [
                'id'             => 2,
                'name'           => 'subadmin1',
                'email'          => 'subadmin1@admin.com',
                'password'       => '$2y$10$XH232IuTQCcPEkbswdv4UeGvuvnj26M1HLKkXtN4Dgx5gyI1WCIMi',
                'phone'               => '0221311',
                'is-sub'              =>true,
                'address'             => 'مشروع القلعة',
                'p_image'             => null,
                'remember_token'      => null,
            ],
            [
                'id'             => 3,
                'name'           => 'subadmin2',
                'email'          => 'subadmin2@admin.com',
                'password'       => '$2y$10$XH232IuTQCcPEkbswdv4UeGvuvnj26M1HLKkXtN4Dgx5gyI1WCIMi',
                'phone'               => '0221311',
                'is-sub'              =>true,
                'address'             => 'مشروع القلعة',
                'p_image'             => null,
                'remember_token'      => null,
            ],
            [
                'id'             => 4,
                'name'           => 'triner',
                'email'          => 'triner@admin.com',
                'password'       => '$2y$10$XH232IuTQCcPEkbswdv4UeGvuvnj26M1HLKkXtN4Dgx5gyI1WCIMi',
                'phone'               => '0221311',
                'is-sub'              =>true,
                'address'             => 'مشروع القلعة',
                'p_image'             => null,

                'remember_token'      => null,
            ],
            [
                'id'             => 5,
                'name'           => 'customer',
                'email'          => 'customer@admin.com',
                'password'       => '$2y$10$XH232IuTQCcPEkbswdv4UeGvuvnj26M1HLKkXtN4Dgx5gyI1WCIMi',
                'phone'               => '0221311',
                'is-sub'              =>true,
                'address'             => 'مشروع القلعة',
                'p_image'             => null,

                'remember_token'      => null,
            ],
        ];

        User::insert($users);
    }
}
