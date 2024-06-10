<?php

namespace App\Models;

use App\Traits\ModelLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public $table="currency";

    protected $tagName = 'Currency';

    public $fillable=[
        'currency',
        'price'
    ];
}
