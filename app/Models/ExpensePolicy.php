<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePolicy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'threshold_amount',
        'requires_receipt',
        'approval_levels',
        'approval_hierarchy',
        'category_restrictions',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'threshold_amount' => 'decimal:2',
        'requires_receipt' => 'boolean',
        'approval_hierarchy' => 'json',
        'category_restrictions' => 'json',
        'active' => 'boolean',
    ];

    /**
     * Scope a query to only include active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Check if an expense amount exceeds the policy threshold.
     */
    public function exceedsThreshold($amount): bool
    {
        return $amount > $this->threshold_amount;
    }

    /**
     * Get the required approvers for a given expense.
     */
    public function getRequiredApprovers($amount)
    {
        $hierarchy = json_decode($this->approval_hierarchy, true) ?? [];
        
        // Determine how many approval levels are needed based on amount
        $requiredLevels = 1; // Default to at least one level
        
        foreach ($hierarchy as $level => $threshold) {
            if ($amount >= $threshold) {
                $requiredLevels = max($requiredLevels, (int)$level + 1);
            }
        }
        
        return min($requiredLevels, $this->approval_levels);
    }
}
