<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'description', 'code', 'status', 'created_by', 'updated_by'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function subs()
    {
        return $this->hasMany(AccountSub::class);
    }

    public function accountSubs(){
        return $this->hasMany(AccountSub::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
