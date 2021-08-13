<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpesa extends Model
{
    use HasFactory;
    protected $fillable = [
        'ido',
        'topic',
        'created_at',
        'eventType',
        'resourceId',
        'status',
        'reference',
        'originationTime',
        'senderPhoneNumber',
        'amount',
        'currency',
        'tillNumber',
        'system',
        'senderFirstName',
        'senderMiddleName',
        'senderLastName',
        'linkSelf',
        'linkResource',
    ];
}
