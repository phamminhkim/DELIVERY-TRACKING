<?php

use App\Utilities\RedisUtility;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompanySeeder::class);
        $this->call(OrderStatusSeeder::class);
        $this->call(OrderReviewOptionSeeder::class);
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(MenuRouterSeeder::class);
        $this->call(RouterSeeder::class);
        $this->call(ExternalDeliveryPartnerSeeder::class);
    }
}
