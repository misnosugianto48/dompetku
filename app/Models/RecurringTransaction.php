<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id', 'category_id', 'type', 'amount', 'description',
        'frequency', 'next_due_date', 'is_active',
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
