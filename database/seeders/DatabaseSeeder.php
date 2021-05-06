<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/* use Database\Seeds\PermissionsTableSeeder;
use Database\Seeds\RolesTableSeeder;
use Database\Seeds\PermissionRoleTableSeeder;
use Database\Seeds\UsersTableSeeder;
use Database\Seeds\RoleUserTableSeeder; */

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
        ]);
        // Seed countries list
        $this->call(CountriesTableSeeder::class);

        // Seed categories
        $this->call(CategoriesTableSeeder::class);

        // Seed attributes
        $this->call(AttributesTableSeeder::class);
        $this->call(AttributeSetsTableSeeder::class);
        $this->call(AttributeAttributeSetTableSeeder::class);
        $this->call(AttributeValuesTableSeeder::class);

        // Seed product with it's dependencies tables
        $this->call(TaxesTableSeeder::class);
        $this->call(ProductGroupsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CategoryProductTableSeeder::class);
        $this->call(AttributeProductValueTableSeeder::class);

        // Seed currencies
        $this->call(CurrenciesTableSeeder::class);

        // Seed carriers
        $this->call(CarriersTableSeeder::class);

        // Seed order statuses
        $this->call(OrderStatusesTableSeeder::class);

        // Seed order statuses
        $this->call(NotificationTemplatesTableSeeder::class);

        // Seed client address
        $this->call(AddressesTableSeeder::class);

        // Seed client company
        $this->call(CompaniesTableSeeder::class);

        // Orders
        $this->call(OrdersTableSeeder::class);
    }

}
