<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'process_date',
        'due_date',
        'customer_name',
        'customer_id',
        'customer_address',
        'previous_balance',
        'contact_person',
        'contact_phone',
        'payment_account',
        'contact_email',
        'notes',
        'signature_image_path',
        'logo_image_path',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->process_date)) {
                $invoice->process_date = Carbon::now()->toDateString();
            }
        });
    }

    protected $casts = [
        'process_date' => 'date',
        'due_date' => 'date',
        'previous_balance' => 'decimal:2',
    ];

    /**
     * Relationship with InvoiceItem
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Calculate the total amount of the invoice
     */
    public function getTotalAttribute()
    {
        return $this->items->sum('total');
    }

    /**
     * Calculate total including previous balance
     */
    public function getGrandTotalAttribute()
    {
        return $this->total + ($this->previous_balance ?? 0);
    }

    /**
     * Check if the invoice is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Format the invoice number
     */
    public function getFormattedInvoiceNumberAttribute()
    {
        return 'INV-' . str_pad($this->invoice_number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for unpaid invoices
     */
    public function scopeUnpaid($query)
    {
        return $query->where('due_date', '<', Carbon::now());
    }

    /**
     * Scope for invoices by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
