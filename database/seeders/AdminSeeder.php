<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\ShippingAddress;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'administrator')->first();

        $admin = \App\Models\User::create([
            'name' => 'مؤسسة دروع المحرك الماسية للتجارة',
            'email' => 'tasnimabdullah2030@gmail.com',
            'password' => bcrypt('A123456789'),
            'phone_number' => '0539829466'
        ]);

        $address = ShippingAddress::create([
            'contact_id' => $admin->id,
            'shipping_address' => '',
            'shipping_city' => 'الرياض',
            'shipping_state' => '',
            'shipping_zip' => '11807',
            'shipping_country' => 'Saudi Arabia',
            'shipping_building_number' => ''
        ]);

        $admin->shippingAddress($address);
        $admin->attachRole($role);

        $admin->attachPermissions($role->permissions);
    }
}
