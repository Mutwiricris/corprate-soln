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
        Schema::create('expense_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('threshold_amount', 10, 2);
            $table->boolean('requires_receipt')->default(true);
            $table->integer('approval_levels')->default(1);
            $table->json('approval_hierarchy')->nullable();
            $table->json('category_restrictions')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        
        // Insert a default policy
        DB::table('expense_policies')->insert([
            'name' => 'Standard Policy',
            'description' => 'Default expense policy for all employees',
            'threshold_amount' => 5000.00,
            'requires_receipt' => true,
            'approval_levels' => 1,
            'approval_hierarchy' => json_encode([
                '0' => 0, // Level 0 (manager) for any amount
                '1' => 10000, // Level 1 (director) for amounts >= 10000
            ]),
            'category_restrictions' => null,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_policies');
    }
};
