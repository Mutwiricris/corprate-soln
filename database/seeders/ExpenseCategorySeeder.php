<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Travel',
                'description' => 'Travel expenses including flights, accommodation, and transportation',
                'color' => '#3B82F6', // Blue
                'active' => true,
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office supplies and equipment',
                'color' => '#10B981', // Green
                'active' => true,
            ],
            [
                'name' => 'Meals & Entertainment',
                'description' => 'Business meals and entertainment expenses',
                'color' => '#F59E0B', // Amber
                'active' => true,
            ],
            [
                'name' => 'Software & Subscriptions',
                'description' => 'Software licenses and subscription services',
                'color' => '#8B5CF6', // Purple
                'active' => true,
            ],
            [
                'name' => 'Training & Education',
                'description' => 'Professional development, courses, and certifications',
                'color' => '#EC4899', // Pink
                'active' => true,
            ],
            [
                'name' => 'Marketing & Advertising',
                'description' => 'Marketing campaigns, advertising, and promotional materials',
                'color' => '#EF4444', // Red
                'active' => true,
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Other business expenses that don\'t fit into other categories',
                'color' => '#6B7280', // Gray
                'active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
