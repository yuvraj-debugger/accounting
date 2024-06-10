<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionReportController extends Controller
{

    private $selectedFinancialYear;

    public function __construct()
    {
        $this->selectedFinancialYear = $_GET['financial_year'] ?? '';
    }

    public function index()
    {
        $accountTypes = [
            'income',
            'expense',
            'profit-expense'
        ];
        $accountModel = new Account();
        $saving = BankAccount::where('bank_account_type', 'saving')->get();

        $financialYearsData = $this->financialYears();
        $calaculatedFiscalYearForDate = $this->calculateFiscalYearForDate();

        return view('transaction-report.index', [
            'selectedFinancialYear' => $this->selectedFinancialYear
        ], compact('financialYearsData', 'calaculatedFiscalYearForDate', 'accountTypes', 'accountModel', 'saving'));
    }

    public function financialYears()
    {
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 year'));
        $years = [];

        for ($i = 4; $i >= 0; $i --) {
            $startYear = $currentYear - $i;
            $endYear = $nextYear - $i;
            $years["$startYear-05-01"] = "$startYear-$endYear";
        }

        return $years;
    }

    public function calculateFiscalYearForDate()
    {
        $selectedDate = $this->selectedFinancialYear ?: date('Y-m-d');
        $startYear = (date('m', strtotime($selectedDate)) > 4) ? date('Y', strtotime($selectedDate)) : date('Y', strtotime('-1 year', strtotime($selectedDate)));
        $endYear = date('Y', strtotime('+1 year', strtotime("$startYear-04-01")));

        $start = new \DateTime("$startYear-04-01");
        $end = new \DateTime("$endYear-04-01");
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);

        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("M Y");
        }

        return $months;
    }

    public static function incomeExpenseReport($accountId, $month, $year)
    {
        return self::calculateAmount('subaccount', $accountId, $month, $year);
    }

    public static function savingsReport($account, $month, $year)
    {
        return self::calculateAmount('transaction_account_type', 'saving', $month, $year);
    }

    private static function calculateAmount($field, $value, $month, $year)
    {
        $transactions = Transaction::where($field, $value)->where('status', 1)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get([
            'amount',
            'account_type'
        ]);

        return $transactions->reduce(function ($carry, $transaction) {
            $amount = (float) str_replace(',', '', $transaction->amount);
            return $transaction->account_type == 'debit' ? $carry - $amount : $carry + $amount;
        }, 0);
    }

    public static function incomeExpenseMonthTotal($month, $year)
    {
        return self::calculateMonthlyTotal('income', $month, $year);
    }

    public static function expenseMonthTotal($month, $year)
    {
        return self::calculateMonthlyTotal('expense', $month, $year);
    }

    public static function profitExpenseMonthTotal($month, $year)
    {
        return self::calculateMonthlyTotal('profit-expense', $month, $year);
    }

    public static function savingMonthTotal($month, $year)
    {
        return self::calculateMonthlyTotal('saving', $month, $year);
    }

    private static function calculateMonthlyTotal($accountType, $month, $year)
    {
        $transactions = Transaction::where('transaction_account_type', $accountType)->where('status', 1)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get([
            'amount',
            'account_type'
        ]);

        return $transactions->reduce(function ($carry, $transaction) {
            $amount = (float) str_replace(',', '', $transaction->amount);
            return $transaction->account_type == 'debit' ? $carry - $amount : $carry + $amount;
        }, 0);
    }
}
