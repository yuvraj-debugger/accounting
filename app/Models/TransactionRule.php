<?php

namespace App\Models;

use App\Traits\ModelLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRule extends Model
{
    use HasFactory;

    protected $table="manage_transactions_rules";

    protected $tagName = 'Transaction Rule';

    protected $fillable = [
        'rule_name',
        'account_type',
        'account_name',
        'wallet_account',
        'payment_account',
        'transactions_category_type',
        'criteria',
        'status',
        'type'
    ];

    protected $casts = [
        'criteria' => 'array'
    ];

    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETE = 2;

    public static function statusOption()
    {
        return [
            self::STATE_ACTIVE => 'Active',
            self::STATE_INACTIVE => 'Inactive',
            self::STATE_DELETE => 'Delete'
        ];
    }

    public function getStatus()
    {
        return isset(self::statusOption()[$this->status])?self::statusOption()[$this->status]:'';
    }

    public function getAccountName() {
        $account = Account::where('id', $this['account_name'])->first();
        return ($account) ? $account->name : '';
    }
}
