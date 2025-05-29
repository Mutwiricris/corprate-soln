<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'currency',
        'category_id',
        'expense_date',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'receipt_number',
        'receipt_path',
        'receipt_content',
        'receipt_mime_type',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the user that owns the expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the expense.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    /**
     * Get the user who approved the expense.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the audit logs for this expense.
     */
    public function auditLogs()
    {
        return $this->hasMany(ExpenseAuditLog::class);
    }

    /**
     * Scope a query to only include expenses with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include expenses within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Check if the expense has a receipt.
     */
    public function hasReceipt(): bool
    {
        return !is_null($this->receipt_path);
    }

    /**
     * Get the receipt URL.
     */
    public function getReceiptUrl(): ?string
    {
        return $this->hasReceipt() ? asset('storage/' . $this->receipt_path) : null;
    }
}
