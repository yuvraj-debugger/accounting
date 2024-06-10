<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use Carbon\Carbon;

class Account extends Model
{
    use HasFactory;

    protected $table = "accounts";

    protected $tagName = 'Accounts';

    protected $fillable = [
        'name',
        'description',
        'parent_account_name',
        'account_type',
        'opening_balance',
        'closing_balance',
        'created_date',
        'type',
        'status',
        'created_by',
        'parent_account',
        'level'
    ];

    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETED = 2;

    const TYPE_INCOME = 'income';

    const TYPE_EXPENSE = 'expense';

    const TYPE_PROFIT_EXPENSE = 'profit-expense';

    const TYPE_SAVING = 'saving';

    public static function stateOptions()
    {
        return [
            self::STATE_INACTIVE => 'Inactive',
            self::STATE_ACTIVE => 'Active',
            self::STATE_DELETED => 'Deleted'
        ];
    }

    public function getStatus()
    {
        return $this->stateOptions()[$this->status];
    }

    public static function typeOptions()
    {
        return [
            self::TYPE_INCOME => 'Income',
            self::TYPE_EXPENSE => 'Expense',
            self::TYPE_PROFIT_EXPENSE => 'Profit Expense',
            self::TYPE_SAVING => 'Saving'
        ];
    }

    public function getType()
    {
        return ($this->account_type) ? $this->typeOptions()[$this->account_type] : '';
    }

    public function scopeSearch($query, $term)
    {
        $term = '%' . $term . '%';

        $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term)
                ->orWhere('full_name', 'like', $term)
                ->orWhere('closing_balance', 'like', $term)
                ->orWhere('created_date', 'like', $term);
        });
    }

    public function getParentName()
    {
        $account = Self::find($this->parent_account_name);
        if (! empty($account)) {
            return $account->name;
        }
    }

    public function tree($account_type)
    {
        $accounts_all = Account::where('account_type', $account_type)->where('status', '!=', 2)->get();
        $rootAccounts = $accounts_all->where('parent_account_name', '');

        $this->formatTree($rootAccounts, $accounts_all);
        return $rootAccounts;
    }

    public function formatTree($rootAccounts, $accounts_all)
    {
        foreach ($rootAccounts as $accounts) {
            $accounts->childern = $accounts_all->where('status', '!=', 2)->where('parent_account_name', $accounts->id);
            if ($accounts->childern->isNotEmpty()) {
                $this->formatTree($accounts->childern, $accounts_all);
            }
        }
    }

    public function getSubAccount()
    {
        $subaccount = Account::where('status', 1)->where('parent_account_name', $this->id)->get();
        return $subaccount;
    }
}

