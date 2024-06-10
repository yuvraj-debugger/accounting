<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionSummaryController extends Controller
{
    public function __construct() {
        $selectedFinancialYear;
    }

    public function index() {
        $accountTypes = ['income','expense','profit-expense'];
        $accountModel = new Account();
        $this->selectedFinancialYear = $_GET['financial_year'] ?? '';
        $saving = BankAccount::where('bank_account_type', 'saving')->get();

        if(!empty($selectedFinancialYear)) {
        } else {
            $financialYearsData = $this->financialYears();
            $calaculatedFiscalYearForDate = $this->calculateFiscalYearForDate();
        }

        return view('transaction-summary.index',[
            'selectedFinancialYear' => $this->selectedFinancialYear,
        ], compact('financialYearsData', 'calaculatedFiscalYearForDate', 'accountTypes', 'accountModel', 'saving'));
    }

    public function financialYears() {
        $month = date('m');
        $years = [];
        if($month > 4){
            $y = date('Y');
            $pt = date('Y', strtotime('+1 year'));
            $fy = $y.' - '.$pt;
        }else{
            $y = date('Y');
            $pt = date('Y', strtotime('+1 year'));
            $fy = $y.' - '.$pt;
        }
        for($i =4; $i >=0 ; $i--){
            $f = $y-$i;
            $l = $pt-$i;
            $years[$f.'-05-01'] = $f.'-'.$l;
        }
        return $years;
    }

    public function calculateFiscalYearForDate() {
        // $this->filtered_date = '2019-05-01';
        if($this->selectedFinancialYear){
            $month = date('m',strtotime($this->selectedFinancialYear));
            if($month > 4){
                $y = date('Y',strtotime($this->selectedFinancialYear));
                $pt = date('Y', strtotime('+1 year', strtotime($this->selectedFinancialYear)));
                $fy = $y."-04-01".":".$pt."-03-31";
            }else{
                $y = date('Y',  strtotime('-1 year', strtotime($this->selectedFinancialYear)));
                $pt = date('Y',strtotime($this->selectedFinancialYear));
                $fy = $y."-04-01".":".$pt."-03-31";
            }
        }else{
            $month = date('n');
            if($month > 4){
                $y = date('Y');
                $pt = date('Y', strtotime('+1 year'));
                $fy = $y."-04-01".":".$pt."-03-31";
            }else{
                $y = date('Y', strtotime('-1 year'));
                $pt = date('Y');
                $fy = $y."-04-01".":".$pt."-03-31";
            }
        }

        $start    = (new \DateTime($y."-04-01"))->modify('first day of this month');
        $end      = (new \DateTime($pt."-04-30"))->modify('first day of this month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);
        $months = array();
        foreach ($period as $dt) {
            $months[] = $dt->format("M Y");
        }
        return $months;
    }

    public static function incomeExpense($accountId, $month, $year) {
        $childAccountIds = Account::where('parent_account_name', $accountId)->pluck('id');

        $transactions = Transaction::whereIn('subaccount', $childAccountIds)
            ->where('status', 1)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get(['amount', 'account_type']);

        $amount = 0.0;

        foreach ($transactions as $transaction) {
            $transactionAmount = (float)str_replace(',', '', $transaction->amount);

            if ($transaction->account_type == 'debit') {
                $amount -= $transactionAmount;
            } else {
                $amount += $transactionAmount;
            }
        }

        return $amount;
    }

    public static function savingsSummary($account, $month, $year) {
        return self::calculateTotal('saving', $month, $year);
    }

    // Income Transactions total
    public static function incomeExpenseMonth($account, $month, $year) {
        return self::calculateTotal('income', $month, $year);
    }

    // Expense Transactions total
    public static function expenseMonthTotal($account, $month, $year) {
        return self::calculateTotal('expense', $month, $year);
    }

    // Profit Expense Transactions total
    public static function profitExpenseMonth($account, $month, $year) {
        return self::calculateTotal('profit-expense', $month, $year);
    }

    // Saving Transactions total
    public static function savingMonthTotal($month, $year) {
        return self::calculateTotal('saving', $month, $year);
    }

    // Function to calculate total based on account type
    private static function calculateTotal($accountType, $month, $year) {
        $transactions = Transaction::where('transaction_account_type', $accountType)
            ->where('status', 1)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get(['amount', 'account_type']);

        $total = 0.0;

        foreach($transactions as $transaction) {
            $transactionAmount = (float)str_replace(',', '', $transaction->amount);
            if ($transaction->account_type == 'debit') {
                $total -= $transactionAmount;
            } else {
                $total += $transactionAmount;
            }
        }

        return $total;
    }
}
