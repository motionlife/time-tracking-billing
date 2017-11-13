<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             US_StatesSeeder::class,
             IndustrySeeder::class,
             PositionSeeder::class,
             TaskgroupSeeder::class,
             TaskSeeder::class,
             OutreferrerSeeder::class,
             ConsultantSeeder::class,
             ClientSeeder::class,
             EngagementSeeder::class,
             HourSeeder::class,
             ExpenseSeeder::class
         ]);
    }
}
