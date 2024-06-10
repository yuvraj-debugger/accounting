<?php

namespace App\Models;

use App\Traits\ModelLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Transaction extends Model
{
    use HasFactory;

    protected $table="transactions";

    protected $tagName = 'Transaction';


    const STATE_INACTIVE=0;

    const STATE_ACTIVE=1;

    const STATE_DELETED=2;

    protected $fillable = [
        'date',
        'bank_description',
        'our_description',
        'bank_account',
        'wallet_account',
        'payment_account',
        'account_type',
        'import_id',
        'credit_type',
        'amount',
        'subaccount',
        'message',
        'type',
        'status',
        'updated_date',
        'created_by',
        'suggest_rule',
        'fixed_rule',
        'istransfer',
        'previous_balance',
        'running_balance',
        'transaction_account_type'
    ];

    public function getBankAccount()
    {
        $bankAccount= BankAccount::where('id',$this->bank_account)->first();
        return ($bankAccount)?$bankAccount->name:'';
    }
    public function getAccount()
    {
        $account=Account::where('id',$this['subaccount'])->first();
        return ($account)?$account->name:'';
    }
    public function getRules()
    {
        if(!empty($this['suggest_rule'])&&!($this['suggest_rule']))
        {
            $rules=TransactionRule::whereIn('id',$this['suggest_rule'])->get()->pluck('rule_name')->toArray();
            return implode(', ',$rules);
        }
        else
        {
            return "";
        }
    }
    public function getsubaccounts()
    {
        if(!empty($this['suggest_rule']))
        {
            $rules=TransactionRule::whereIn('id',$this['suggest_rule'])->get()->pluck('account_name')->toArray();
            if(!empty($rules))
            {
                $account=Account::whereIn('id',$rules)->get()->pluck('name')->toArray();
                return implode(', ',$account);
            }
            else
            {
                return "";
            }
        }
        else
        {
            return "";
        }
    }
    public function getaccountName($accounts){
        $account=Account::whereIn('id',explode(',',$accounts))->get()->pluck('name')->toArray();
        return implode(', ',$account);
    }
    public function getRuleName($rule_id)
    {
        $rules = TransactionRule::where('id',$rule_id)->first();
        return !empty($rules) ? $rules->rule_name: '';
    }
    public function getistransfer()
    {
        $account=Transaction::where('id',$this->istransfer)->first();

    }

    public function getfixedRule()
    {
        $fixedrules=TransactionRule::where('id', $this->fixed_rule)->first();
        return ($fixedrules)?$fixedrules->rule_name:'';
    }

    public function getRuleAccountName($accountId) {
        $account = Account::where('id', $accountId)->first();
        return $account->name;
    }

    public function getRuleAccountId($ruleId) {
        $rules = TransactionRule::where('id',$ruleId)->first();
        return $rules->account_name;
    }

    public static function getAccountDetails($accountId) {
        $account = Account::where('id', $accountId)->first();
        return $account;
    }

    public static function getBankAccountDetails($id) {
        $account = BankAccount::where('id', $id)->first();
        return $account;
    }

    public function getBankOpeningBalance() {
        $bank = BankAccount::where('id', $this->bank_account)->first();
        return $bank ?? $bank->opening_balance;
    }

    public function getBankBalance() {
        $bank = BankAccount::where('id', $this->bank_account)->first();
        return $bank ?? $bank->getBalance();
    }
}
