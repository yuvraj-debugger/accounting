<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionRule;

class TransactionRuleController extends Controller
{

    public function index() {
        $search = $_GET['search_rule'] ?? '';
        $transactionRules = TransactionRule::where('status', TransactionRule::STATE_ACTIVE)->orderBy('id','DESC');

        if(! empty($search)){
            $ids_banks = Account::where('name', 'like', '%' . trim($search) . '%')->pluck('id')->toArray();

            $transactionRules = $transactionRules->where('status', TransactionRule::STATE_ACTIVE)
                                ->whereIn('account_name', $ids_banks)
                                ->where('rule_name','like','%'.$search.'%')
                                ->orWhere('account_type','like','%'.$search.'%')
                                ->orWhere(function ($query) use($search) {
                                    $query->whereRaw("JSON_EXTRACT(criteria, '$.value') LIKE ?", [
                                        '%' . $search . '%'
                                    ]);
                                })
                                ->orWhere(function ($query) use($search){
                                    $query->whereRaw("JSON_EXTRACT(criteria, '$.description') LIKE ?", [
                                        '%' . $search . '%'
                                    ]);
                                })
                                ->orWhere('transactions_category_type', 'like', '%' . $search . '%');
        }

        $transactionRules = $transactionRules->paginate(10);
        return view('transaction-rule.index',compact('transactionRules', 'search'));
    }


    public function create() {
        $accounts=Account::where('status',Account::STATE_ACTIVE)->get();
        $wallets=BankAccount::where('bank_account_type',BankAccount::TYPE_WALLET)->get();
        $paymentGateways=BankAccount::where('bank_account_type',BankAccount::TYPE_PAYMENT_GATEWAY)->get();
        return view('transaction-rule.create',compact('accounts','wallets','paymentGateways'));
    }


    public function store(Request $request) {
        $request->validate([
            'rule_name' => 'required',
            'account_type' => 'required',
            'criteria' => 'required',
            'description_type' => 'required|array|min:1',
            'description_type.*' => 'required',
            'like' => 'required|array|min:1',
            'like.*' => 'required',
            'value' => 'required|array|min:1',
            'value.*' => 'required',
            'account' => 'required',
        ], [
            'description_type.*.required' => 'Please select a description type.',
            'like.*.required' => 'Please select a like option.',
            'value.*.required' => 'Please enter a value.',
        ]);

        $criteriaJson = [];
        foreach($request->description_type as $key => $value){
            $description = $moreValues[$key] = $value;
            $criteriaJson['description'][] = $description;
        }
        foreach($request->like as $key => $value){
            $likes = $moreValues[$key] = $value;
            $criteriaJson['condition'][] = $likes;
        }
        foreach($request->value as $key => $value){
            $value = $moreValues[$key] = $value;
            $criteriaJson['value'][] = $value;
        }

        $transactionRule = new TransactionRule();
        $transactionRule->rule_name = $request->rule_name;
        $transactionRule->account_type = $request->account_type;
        $transactionRule->transactions_category_type = $request->criteria;
        $transactionRule->account_name = $request->account;
        $transactionRule->wallet_account = $request->wallet_account;
        $transactionRule->payment_account = $request->payment_account;
        $transactionRule->criteria = $criteriaJson;
        $transactionRule->save();

        $transactionRule = TransactionRule::find($transactionRule->id);
        $suggest_rule = [$transactionRule->id];

        $transactions = Transaction::where('status', 1)->where('subaccount', null)->where('suggest_rule', null);

        if($transactions) {
            if($transactionRule->transactions_category_type == 'anyone_matching_criteria') {
                if($transactionRule->account_type == 'debit') {
                    $transactions = $transactions->where('account_type', 'debit');
                    $transactions = self::createOrSubLikeQuery($transactions, $transactionRule);

                } else {
                    $transactions = $transactions->where('account_type', 'credit');
                    $transactions = self::createOrSubLikeQuery($transactions, $transactionRule);
                }

            } else {
                if($transactionRule->account_type == 'debit') {
                    $transactions = $transactions->where('account_type', 'debit');
                    $transactions = self::createLikeQuery($transactions, $transactionRule);

                } else {
                    $transactions = $transactions->where('account_type', 'credit');
                    $transactions = self::createLikeQuery($transactions, $transactionRule);

                }
            }

            self::updateTransactionRule($transactions, $transactionRule);

            $transactionRule->type = 1;
            $transactionRule->save();
        }

        return redirect()->route('transaction-rule.index')->with('success', 'Rule created successfully');
    }

    public static function createLikeQuery($transactions, $rule) {
        $j = 0;
        foreach($rule->criteria['description'] as $description) {
            $transactions = $transactions->where($description, $rule->criteria['condition'][$j], '%'.$rule->criteria['value'][$j].'%');
            ++$j;
        }
        return $transactions;
    }

    public static function createOrSubLikeQuery($transactions, $rule) {
        $transactions = $transactions->where(function($query) use($rule) {
            for($j=0; $j < count($rule->criteria['description']); $j++) {
                $query->orWhere($rule->criteria['description'][$j], $rule->criteria['condition'][$j], '%'.$rule->criteria['value'][$j].'%');
            }
        });
        return $transactions;
    }

    public static function updateTransactionRule($transactions, $rule) {
        if($transactions) {
            foreach($transactions->get() as $transaction)
            {
                $suggest_rule = [$rule->id];
                $transaction->suggest_rule = $suggest_rule;
                $transaction->update();
            }
        }
    }


    public function update($id) {
        $accounts=Account::where('status',Account::STATE_ACTIVE)->get();
        $wallets=BankAccount::where('bank_account_type',BankAccount::TYPE_WALLET)->get();
        $paymentGateways=BankAccount::where('bank_account_type',BankAccount::TYPE_PAYMENT_GATEWAY)->get();
        $transaction = TransactionRule::find($id);
        return view('transaction-rule.update', ['transaction' => $transaction, 'accounts' => $accounts, 'wallets' => $wallets, 'paymentGateways' => $paymentGateways]);
    }


    public function storeupdate(Request $request, $id) {
        $request->validate([
            'rule_name' => 'required',
            'account_type' => 'required',
            'criteria' => 'required',
            'description_type' => 'required',
            'like' => 'required',
            'value' => 'required',
            'account' => 'required',
        ]);

        $criteriaJson = [];
        foreach($request->description_type as $key => $value){
            $description = $moreValues[$key] = $value;
            $criteriaJson['description'][] = $description;
        }
        foreach($request->like as $key => $value){
            $likes = $moreValues[$key] = $value;
            $criteriaJson['condition'][] = $likes;
        }
        foreach($request->value as $key => $value){
            $value = $moreValues[$key] = $value;
            $criteriaJson['value'][] = $value;
        }

        $transactionRule = TransactionRule::find($id);
        $transactionRule->rule_name = $request->rule_name;
        $transactionRule->account_type = $request->account_type;
        $transactionRule->transactions_category_type = $request->criteria;
        $transactionRule->account_name = $request->account;
        $transactionRule->wallet_account = $request->wallet_account;
        $transactionRule->payment_account = $request->payment_account;
        $transactionRule->criteria = $criteriaJson;
        $transactionRule->save();

        $transactionRule = TransactionRule::find($transactionRule->id);
        $suggest_rule = [$transactionRule->id];

        $transactions = Transaction::where('status', 1)->where('subaccount', null)->where('suggest_rule', null);

        if($transactions) {
            if($transactionRule->transactions_category_type == 'anyone_matching_criteria') {
                if($transactionRule->account_type == 'debit') {
                    $transactions = $transactions->where('account_type', 'debit');
                    $transactions = self::createOrSubLikeQuery($transactions, $transactionRule);

                } else {
                    $transactions = $transactions->where('account_type', 'credit');
                    $transactions = self::createOrSubLikeQuery($transactions, $transactionRule);
                }

            } else {
                if($transactionRule->account_type == 'debit') {
                    $transactions = $transactions->where('account_type', 'debit');
                    $transactions = self::createLikeQuery($transactions, $transactionRule);

                } else {
                    $transactions = $transactions->where('account_type', 'credit');
                    $transactions = self::createLikeQuery($transactions, $transactionRule);

                }
            }

            self::updateTransactionRule($transactions, $transactionRule);

            $transactionRule->type = 1;
            $transactionRule->save();
        }

        return redirect()->route('transaction-rule.index')->with('success', 'Rule updated successfully');
    }


    public function delete($id) {
        $transaction = TransactionRule::find($id);
        $transaction->status = TransactionRule::STATE_DELETE;
        $transaction->save();
        return redirect()->route('transaction-rule.index')->with('danger', 'Rule deleted successfully');
    }
}
