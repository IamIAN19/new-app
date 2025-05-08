<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'added_date' => 'date',
        'total_amount' => 'float',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (!$invoice->company_id) {
                throw new \Exception("Cannot generate invoice code without company_id.");
            }
    
            $invoice->code = \App\Services\InvoiceCodeGenerator::generate($invoice->company_id);
        });
    }
   
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'tin', 'tin');
    }

    public function supplierData()
    {
        return $this->belongsTo(Supplier::class, 'tin', 'tin');
    }

    public function invoiceOthers()
    {
        return $this->hasMany(InvoicesOtherExpenses::class, 'invoice_id');
    }

    public function category()
    {
        return $this->belongsTo(SalesCategory::class, 'sales_category_id');
    }
}
