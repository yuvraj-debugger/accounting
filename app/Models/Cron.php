<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'file_name',
        'bank_account',
        'sub_account',
        'updated_records',
        'total_records'
    ];
}
