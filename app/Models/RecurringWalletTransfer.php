<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringWalletTransfer extends Model
{
    protected $fillable = [
        'source_id',
        'target_id',
        'start_date',
        'end_date',
        'frequency',
        'email',
        'amount',
        'reason'
    ];

    
    
}
