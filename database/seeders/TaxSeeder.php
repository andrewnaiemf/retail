<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Tax::create([
            'name' => 'VAT',
            'rate' => 15,
        ]);

        Tax::create([
            'name' => 'Vat',
            'rate' => 0,
        ]);

        Tax::create([
            'name' => 'Vat',
            'rate' => 'Exempt'
        ]);
    }
}
