<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship with Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate the total amount for this item
     */
    public function getTotalAttribute()
    {
        return $this->amount;
    }

    /**
     * Format the amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Scope for items by invoice
     */
    public function scopeByInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    /**
     * Scope for items with amount greater than specified value
     */
    public function scopeAmountGreaterThan($query, $amount)
    {
        return $query->where('amount', '>', $amount);
    }

    /**
     * Scope for items with amount less than specified value
     */
    public function scopeAmountLessThan($query, $amount)
    {
        return $query->where('amount', '<', $amount);
    }
}
