<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'slug' => 'basic',
                'description' => 'Perfect for individual teachers getting started.',
                'price' => 0.00,
                'duration_days' => 30,
                'features' => ['Max 5 Courses', '100 Students', 'Basic Analytics', 'Community Access'],
                'is_active' => true,
            ],
            [
                'name' => 'Pro Plan',
                'slug' => 'pro',
                'description' => 'Unlock advanced teaching tools and more students.',
                'price' => 19.99,
                'duration_days' => 30,
                'features' => ['Unlimited Courses', '1000 Students', 'Advanced Analytics', 'Test Series', 'Priority Support'],
                'is_active' => true,
            ],
            [
                'name' => 'Business Plan',
                'slug' => 'business',
                'description' => 'For coaching centers and large teams.',
                'price' => 49.99,
                'duration_days' => 30,
                'features' => ['Everything in Pro', 'Unlimited Students', 'Custom Domain', 'Team Management', 'White-labeling'],
                'is_active' => true,
            ],
            [
                'name' => 'Yearly Pro',
                'slug' => 'yearly-pro',
                'description' => 'Save 20% with our annual professional plan.',
                'price' => 199.99,
                'duration_days' => 365,
                'features' => ['Everything in Pro', 'Annual Billing', 'Bonus Marketing Tools'],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
