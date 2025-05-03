<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicesOtherExpenses extends Model
{
    use HasFactory;

    public $table = "invoices_other_expenses";

    protected $guarded = [];

    public function accountTitle()
    {
        return $this->belongsTo(AccountTitle::class, 'account_title_id');
    }

    public function accountSub()
    {
        return $this->belongsTo(AccountSub::class, 'account_sub_id');
    }

    public function invoiceSubs()
    {
        return $this->hasMany(InvoiceSub::class, 'invoice_other_expenses_id');
    }
}
