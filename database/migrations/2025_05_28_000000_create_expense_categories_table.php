<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color', 7)->default('#6B7280'); // Default gray color
            $table->boolean('active')->default(true);
            $table->float('assign_amount')->default(0);
            $table->timestamps();
        });
        
        // Insert default categories
        DB::table('expense_categories')->insert([
            [
                'name' => 'Travel',
                'description' => 'Travel expenses including flights, accommodation, and transportation',
                'color' => '#3B82F6', // Blue
                'active' => true,
                'assign_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office supplies and equipment',
                'color' => '#10B981', // Green
                'active' => true,
                'assign_amount' => 0,
                    'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meals & Entertainment',
                'description' => 'Business meals and entertainment expenses',
                'color' => '#F59E0B', // Amber
                'active' => true,
                'assign_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Software & Subscriptions',
                'description' => 'Software licenses and subscription services',
                'color' => '#8B5CF6', // Purple
                'active' => true,
                'assign_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Other business expenses',
                'color' => '#6B7280', // Gray
                'active' => true,
                'assign_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
