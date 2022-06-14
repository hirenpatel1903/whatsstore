<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Plan::create(
            [
                'name' => 'Free Plan',
                'price' => 0,
                'duration' => 'Unlimited',
                'max_stores' => 2,
                'max_products' => 5,
                'enable_custdomain' => 'on',
                'enable_custsubdomain' => 'on',
                'shipping_method' => 'on',
                'description' => 'For companies that need a robust full-featured time tracker.',
                'image' => 'free_plan.png',
            ]
        );
    }
}
