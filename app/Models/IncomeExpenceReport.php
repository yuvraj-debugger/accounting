<?php

namespace App\Models;

use App\Traits\ModelLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeExpenceReport extends Model
{
    use HasFactory;

    protected $table = "income_expence_reports";

    protected $tagName = 'Income Expence Reports';

    public $fillable=[
       'account',
       'year',
       'month',
       'balance'
    ];
}

