<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSub extends Model
{
    use HasFactory;

    public $table = "invoice_subs";

    protected $guarded = [];

    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id');
    }

    public function invoicesOtherExpense()
    {
        return $this->belongsTo(InvoicesOtherExpenses::class, 'invoice_other_expenses_id');
    }
}
