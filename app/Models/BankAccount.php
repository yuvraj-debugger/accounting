<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = "bank_accounts";

    protected $tagName = 'Bank Accounts';

    protected $fillable = [
        'name',
        'description',
        'bank_account_type',
        'opening_balance',
        'currency',
        'type',
        'status',
        'created_by',
        'running_balance'
    ];

    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETE = 2;

    const TYPE_BANK = 1;

    const TYPE_CREDIT_CARD = 2;

    const TYPE_WALLET=3;

    const TYPE_CASH = 4;

    const TYPE_SAVING=5;

    const TYPE_PAYMENT_GATEWAY = 6;

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

    public static function accountTypeOption()
    {
        return [
            self::TYPE_BANK => 'Bank',
            self::TYPE_CREDIT_CARD => 'Credit Card',
            self::TYPE_WALLET => 'Wallet',
            self::TYPE_CASH => 'Cash',
//             self::TYPE_SAVING => 'Saving',
            self::TYPE_PAYMENT_GATEWAY => 'Payment Gateway'
        ];
    }
    public function getAccountType()
    {
        $accountTypeOptions = self::accountTypeOption();
        return isset($accountTypeOptions[$this->bank_account_type]) ? $accountTypeOptions[$this->bank_account_type] : '';
    }
    public function scopeSearch($query, $term)
    {
        $term = '%' . $term . '%';
        $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term)->orWhere('description', 'like', $term)->orWhere('account_type', 'like', $term);
        });
    }

    public function getCurrency()
    {
        $currency = Currency::find($this->currency);
        return $currency ? $currency->currency : '';
    }

    public function getBalance()
    {
        $amount = 0;
        $debit_amount = 0;
        $credits = Transaction::where('account_type', 'credit')->where('bank_account', $this->id)->get();
        $debits = Transaction::where('account_type', 'debit')->where('bank_account', $this->id)->get();
        foreach ($credits as $credit) {
            $amount += (float)$credit['amount'];
        }
        foreach ($debits as $debit) {
            $debit_amount += (float)$debit['amount'];
        }
        return $amount + $this['opening_balance'] - $debit_amount;
    }
}
