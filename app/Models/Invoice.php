<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'quotation_id',
        'invoice_date',
        'payment_due',
        'amount',
        'time_difference',
        'current_time',
        'status',
        'statas',
    ];
    public function quotation(){
        return $this->belongsTo(Quotation::class);
    }
}
