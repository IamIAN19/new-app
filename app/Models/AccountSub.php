<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountSub extends Model
{
    use HasFactory;

    protected $fillable = ['account_title_id', 'name', 'code'];

    public $table = 'account_sub';

    public function accountTitle()
    {
        return $this->belongsTo(AccountTitle::class);
    }
}
