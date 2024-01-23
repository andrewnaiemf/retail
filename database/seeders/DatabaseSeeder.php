<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();
        // $this->call(LaratrustSeeder::class);
        // $this->call(SuperAdminSeeder::class);
        // $this->call(TaxSeeder::class);
        // $this->call(SpetialTaxReasonSeeder::class);
        $this->call(AdminSeeder::class);
    }
}
