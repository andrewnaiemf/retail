<?php

namespace Database\Seeders;

use App\Models\SpecialTaxReason;
use Illuminate\Database\Seeder;

class SpetialTaxReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        SpecialTaxReason::create([
            'name' => 'Export of goods'
        ]);

        SpecialTaxReason::create([
            'name' => 'Export of services'
        ]);

        SpecialTaxReason::create([
            'name' => 'Export of goods'
        ]);

        SpecialTaxReason::create([
            'name' => 'Private education to citizen'
        ]);

        SpecialTaxReason::create([
            'name' => 'Private healthcare to citizen'
        ]);

        SpecialTaxReason::create([
            'name' => 'Medicines and medical equipment'
        ]);

        SpecialTaxReason::create([
            'name' => 'International transport of Goods'
        ]);
    }
}
