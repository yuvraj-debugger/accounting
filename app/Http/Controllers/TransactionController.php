<?php
namespace App\Http\Controllers;

use App\Models\Cron;
use App\Models\Account;
use App\Models\ImportFile;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Validator;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        $search = $_GET['search_transaction'] ?? '';
        $search_matching = $_GET['search_matching'] ?? '';
        $search_bank_account = $_GET['bank_account'] ?? '';
        $search_account_type = $_GET['account_type'] ?? '';
        $search_subaccount = $_GET['subaccount'] ?? '';
        $search_custom_date = $_GET['custom_date'] ?? '';
        $search_filter_wallet = $_GET['filter_wallet_account'] ?? '';
        $search_filter_payment_credit = $_GET['filter_payment_credit'] ?? '';
        $search_transaction_account = $_GET['transaction_account'] ?? '';

        $query = $request->query();

        $bankAccounts = BankAccount::where('status', 1)->get();
        $mainAccounts = Account::where('status', Account::STATE_ACTIVE)->whereNull('parent_account_name')->get();
        $walletAccounts = BankAccount::where('bank_account_type', BankAccount::TYPE_WALLET)->where('status', 1)->get();
        $paymentWallets = BankAccount::where('bank_account_type', BankAccount::TYPE_PAYMENT_GATEWAY)->where('status', 1)->get();
        $importedFiles = ImportFile::where('status', 1)->get();
        $perPage = ($request->input('per_page'))?$request->input('per_page'):20;
        $transactions = self::filteredTransactions($search,$search_matching, $search_bank_account, $search_account_type, $search_subaccount, $search_custom_date, $search_filter_wallet, $search_filter_payment_credit, $search_transaction_account, $perPage);

        $transactions = $transactions->paginate($perPage);

        $parentAccounts = Account::where('status', 1)->where('parent_account_name', null)->get();
        $accountTypes = Account::typeOptions();
        return view('transaction.index', compact('transactions', 'search', 'bankAccounts', 'mainAccounts', 'importedFiles', 'walletAccounts', 'paymentWallets', 'query', 'parentAccounts', 'accountTypes'));
    }

    public function create()
    {
        $subAccounts = Account::where('status', Account::STATE_ACTIVE)->get();
        $bankAccounts = BankAccount::where('status', 1)->get();
        $walletAccounts = BankAccount::where('bank_account_type', BankAccount::TYPE_WALLET)->where('status', 1)->get();
        $paymentWallets = BankAccount::where('bank_account_type', BankAccount::TYPE_PAYMENT_GATEWAY)->where('status', 1)->get();
        return view('transaction.create', compact('subAccounts', 'bankAccounts', 'walletAccounts', 'paymentWallets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'account_type' => 'required',
            'bank_description' => 'required',
            'our_description' => 'required',
            'amount' => 'required'
        ]);
        if ($request->subaccount) {
            $transactionAccountType = Transaction::getAccountDetails($request->subaccount);
        }

        $transaction = new Transaction();
        $transaction->date = $request->date;
        $transaction->account_type = $request->account_type;
        $transaction->bank_description = $request->bank_description;
        $transaction->our_description = $request->our_description;
        $transaction->amount = $request->amount;
        $transaction->subaccount = $request->subaccount;
        $transaction->bank_account = $request->bank_account;
        $transaction->wallet_account = $request->wallet_account;
        $transaction->payment_account = $request->payment_credit;
        $transaction->transaction_account_type = ($request->subaccount) ? $transactionAccountType->account_type : '';
        $transaction->status = 1;
        $transaction->save();

        $rule = TransactionRule::where('type', 0)->first();
        if ($rule) {
            self::addSuggestedRule($rule, $transaction);
        }

        return redirect('/transaction')->with('success', 'Transaction added successfully');
    }

    public function update($id)
    {
        $bankAccounts = BankAccount::where('status', 1)->get();
        $walletAccounts = BankAccount::where('bank_account_type', BankAccount::TYPE_WALLET)->where('status', 1)->get();
        $paymentWallets = BankAccount::where('bank_account_type', BankAccount::TYPE_PAYMENT_GATEWAY)->where('status', 1)->get();
        $transaction = Transaction::find($id);
        $subAccounts = Account::where('status', Account::STATE_ACTIVE)->get();
        return view('transaction.update', compact('bankAccounts', 'walletAccounts', 'paymentWallets', 'transaction', 'transaction', 'subAccounts'));
    }

    public function storeupdate(Request $request, $id)
    {
        $request->validate([
            'date' => 'required',
            'account_type' => 'required',
            'bank_description' => 'required',
            'our_description' => 'required',
            'amount' => 'required'
        ]);
        $transaction = Transaction::find($id);
        $transaction->date = $request->date;
        $transaction->account_type = $request->account_type;
        $transaction->bank_description = $request->bank_description;
        $transaction->our_description = $request->our_description;
        $transaction->amount = $request->amount;
        $transaction->subaccount = $request->subaccount;
        $transaction->bank_account = $request->bank_account;
        $transaction->wallet_account = $request->wallet_account;
        $transaction->payment_account = $request->payment_credit;
        $transaction->save();
        return redirect('/transaction')->with('success', 'Transaction updated successfully');
    }

    public function delete($id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = 2;
        $transaction->update();
        return redirect('/transaction')->with('info', 'Transaction deleted successfully');
    }

    public function deleteAll(Request $request)
    {
        $resultArray = [];
        $query = $request->input('query');
        if (! empty($query)) {
            $query = $request->input('query');

            foreach (explode('&', $query) as $item) {
                list ($key, $value) = explode('=', $item);
                $resultArray[$key] = $value;
            }
        }

        $search = '';
        $search_bank_account = '';
        $search_account_type = '';
        $search_subaccount = '';
        $search_custom_date = '';
        $search_filter_wallet = '';
        $search_filter_payment_credit = '';
        $search_transaction_account = '';

        if (! empty($resultArray)) {
            $search = $resultArray['search_transaction'] ?? '';
            $search_matching = $resultArray['search_matching'] ?? '';
            $search_bank_account = $resultArray['bank_account'] ?? '';
            $search_account_type = $resultArray['account_type'] ?? '';
            $search_subaccount = $resultArray['subaccount'] ?? '';
            $search_custom_date = $resultArray['custom_date'] ?? '';
            $search_filter_wallet = $resultArray['filter_wallet_account'] ?? '';
            $search_filter_payment_credit = $resultArray['filter_payment_credit'] ?? '';
            $search_transaction_account = $resultArray['transaction_account'] ?? '';
        }

        if (isset($request->all_ids)) {
            $transactions = Transaction::whereIn('id', explode(',', $request->all_ids))->get();
        } else {
            $transactions = self::filteredTransactions($search,$search_matching, $search_bank_account, $search_account_type, $search_subaccount, $search_custom_date, $search_filter_wallet, $search_filter_payment_credit, $search_transaction_account,$perPage);
            $transactions = $transactions->get();
        }

        foreach ($transactions as $transaction) {
            $transaction->status = 2;
            $transaction->update();
        }
    }

    public function downloadSampleImportFile()
    {
        $filename = 'bank_transaction.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];
        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Date',
                'Debit',
                'Credit',
                'Balance',
                'Our Description',
                'Bank Description'
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    public function importTransactions(Request $request)
    {
        $file = $request->file;
        $skipped = array(
            '0'
        );
        if ($file) {
            $filename = $file->getClientOriginalName();
            $import_id = ImportFile::create([
                'file_name' => $filename,
                'status' => 1
            ]);

            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $rule = TransactionRule::where('type', 0)->first();

            foreach ($data as $key => $row) {
                // skipping column names
                if (in_array($key, $skipped)) {
                    continue;
                }

                $date = $row[1];
                $debit = $row[2];
                $credit = $row[3];
                $balance = $row[4];
                $ourDescription = $row[5];
                $bankDescription = $row[6];

                $dateString = str_replace('/', '-', $date);
                $dateTime = date('Y-m-d', strtotime($dateString));
                if (! empty(array_filter($row))) {

                    $transaction = Transaction::create([
                        'date' => $dateTime,
                        'account_type' => ! empty($debit) ? 'debit' : 'credit',
                        'amount' => ! empty($debit) ? $debit : $credit,
                        'bank_account' => $request->bank_account,
                        'balance' => $balance,
                        'bank_description' => $bankDescription,
                        'our_description' => $ourDescription,
                        'import_id' => $import_id->id,
                        'status' => 1
                    ]);

                    // Update suggested rule for each imported transaction
                    if ($rule && $transaction) {
                        self::addSuggestedRule($rule, $transaction);
                    }
                }
            }

            // Update rule type
            if ($rule) {
                $rule->type = 1;
                $rule->save();
            }
        }
        return redirect('/transaction')->with('success', 'File imported successfully');
    }

    public function applySuggestedAccount(Request $request)
    {
        $transactionAccountType = Transaction::getAccountDetails($request->accountId);
        $transaction = Transaction::where('id', $request->transactionId)->update([
            'subaccount' => $request->accountId,
            'transaction_account_type' => $transactionAccountType->account_type
        ]);

        Session::flash('success', 'Transaction updated successfully.');

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully.'
        ]);
    }

    public function addSuggestedRule($rule, $transaction)
    {
        $suggest_rule = [
            $rule->id
        ];
        $transaction = Transaction::find($transaction->id);

        if ($rule->transactions_category_type == 'anyone_matching_criteria') {
            if ($rule->account_type == 'debit') {
                if ($transaction->account_type == 'debit') {
                    $transaction->suggest_rule = $suggest_rule;
                    $transaction->save();
                }
            } else if ($rule->account_type == 'credit') {
                if ($transaction->account_type == 'credit') {
                    $transaction->suggest_rule = $suggest_rule;
                    $transaction->save();
                }
            }
        } else {
            if ($rule->account_type == 'debit') {
                if ($transaction->account_type == 'debit') {
                    $transaction->suggest_rule = $suggest_rule;
                    $transaction->save();
                }
            } elseif ($rule->account_type == 'credit') {
                if ($transaction->account_type == 'credit') {
                    $transaction->suggest_rule = $suggest_rule;
                    $transaction->save();
                }
            }
        }
    }

    public function deleteImportedFile(Request $request)
    {
        $importedFileId = $request->imported_file;

        $transactionsImportFile = ImportFile::where('id', $importedFileId)->first();
        $transactions = Transaction::where('import_id', $importedFileId)->get();
        foreach ($transactions as $data) {
            $data->update([
                'status' => 2
            ]);
        }
        $transactionsImportFile->update([
            'status' => 2
        ]);

        return redirect('/transaction')->with('success', 'Imported File deleted successfully');
    }

    public function editAll(Request $request)
    {
        $resultArray = [];
        $query = $request->input('query');
        if (! empty($query)) {
            $query = $request->input('query');

            foreach (explode('&', $query) as $item) {
                list ($key, $value) = explode('=', $item);
                $resultArray[$key] = $value;
            }
        }

        $search = '';
        $search_bank_account = '';
        $search_account_type = '';
        $search_subaccount = '';
        $search_custom_date = '';
        $search_filter_wallet = '';
        $search_filter_payment_credit = '';
        $search_transaction_account = '';
        $search_matching = '';
        $perPage = ($request->input('per_page'))?$request->input('per_page'):20;

        if (! empty($resultArray)) {
            $search = $resultArray['search_transaction'] ?? '';
            $search_bank_account = $resultArray['bank_account'] ?? '';
            $search_matching = $resultArray['search_matching'] ?? '';
            $search_account_type = $resultArray['account_type'] ?? '';
            $search_subaccount = $resultArray['subaccount'] ?? '';
            $search_custom_date = $resultArray['custom_date'] ?? '';
            $search_filter_wallet = $resultArray['filter_wallet_account'] ?? '';
            $search_filter_payment_credit = $resultArray['filter_payment_credit'] ?? '';
            $search_transaction_account = $resultArray['transaction_account'] ?? '';
        }

        if (! empty($request->all_edit_ids)) {
            // Bulk edit for selected IDs
            $bulkIds = explode(',', $request->all_edit_ids);
            $transactions = Transaction::whereIn('id', $bulkIds)->get();
            self::updateTransaction($transactions, $request);

        } else {
            // Bulk edit all transactions
            $transactions = self::filteredTransactions($search, $search_matching, $search_bank_account, $search_account_type, $search_subaccount, $search_custom_date, $search_filter_wallet, $search_filter_payment_credit, $search_transaction_account, $perPage);

            if (! empty($request->bank_account)) {
                $data['bank_account'] = $request->bank_account;
            }
            if ($request->account_type) {
                $data['account_type'] = $request->account_type;
            }
            if ($request->subaccount) {
                $data['subaccount'] = $request->subaccount;
                $account = Account::where('id', $request->subaccount)->first();
                $data['transaction_account_type'] = $account ? $account->account_type : '';
            }
            if ($request->edit_wallet_account) {
                $data['edit_wallet_account'] = $request->edit_wallet_account;
            }
            if ($request->edit_payment_credit) {
                $data['edit_payment_credit'] = $request->edit_payment_credit;
            }

            $transactions->update($data);

            // self::updateTransaction($transactions->get(),$request);
        }

        return redirect('/transaction')->with('success', 'Transaction updated successfully');
    }

    public static function filteredTransactions($search,$search_matching, $search_bank_account, $search_account_type, $search_subaccount, $search_custom_date, $search_filter_wallet, $search_filter_payment_credit, $search_transaction_account,$perPage)
    {
        $transactions = Transaction::where('status', Transaction::STATE_ACTIVE)->orderBy('id', 'DESC');

        if ((! empty($search)) || (! empty($search_matching))) {
            if(($search_matching == 0) && ($search)){
                $transactions = $transactions->where(function ($query) use ($search) {
                    $query->whereNotIn('bank_account', function ($subquery) use ($search) {
                        $subquery->select('id')->from('bank_accounts')->where('name', '=', $search);
                    })
                    ->whereNotIn('subaccount', function ($subquery) use ($search) {
                        $subquery->select('id')->from('accounts')->where('name', '=', $search);
                    })
                    ->where('bank_description', '!=', $search)
                    ->where('our_description', '!=', $search)
                    ->where('amount', '!=', $search);
                });
//                     dd($transactions->get());
            }else{
                $transactions = $transactions
                ->where(function ($query) use ($search) {
                    $query->orWhereIn('bank_account', function ($query) use ($search) {
                        $query->select('id')
                        ->from('bank_accounts')
                        ->where('name', 'like', '%' . trim($search) . '%');
                    })
                    ->orWhereIn('subaccount', function ($query) use ($search) {
                        $query->select('id')
                        ->from('accounts')
                        ->where('name', 'like', '%' . trim($search) . '%');
                    })
                    ->orWhere('bank_description', 'like', '%' . trim($search) . '%')
                    ->orWhere('our_description', 'like', '%' . trim($search) . '%')
                    ->orWhere('amount', 'like', '%' . trim($search) . '%');
                });
            }

        }
        if (! empty($search_bank_account)) {
            $transactions = $transactions->where('status', 1)
                ->orderBy('id', 'DESC')
                ->where('bank_account', $search_bank_account);
        }
        if (! empty($search_account_type)) {
            $transactions = $transactions->where('status', 1)
                ->orderBy('id', 'DESC')
                ->where('account_type', $search_account_type);
        }
        if (! empty($search_subaccount)) {
            $transactions = $transactions->where('status', 1)
                ->orderBy('id', 'DESC')
                ->where('subaccount', $search_subaccount);
        }
        if (! empty($search_custom_date)) {
            $custom_date = explode('-', $search_custom_date);
            $transactions = $transactions->where('status', 1)
                ->orderBy('id', 'DESC')
                ->whereBetween('date', [
                date('Y-m-d', strtotime($custom_date[0])),
                date('Y-m-d', strtotime($custom_date[1]))
            ]);
        }
        if (! empty($search_filter_wallet)) {
            $transactions = $transactions->where('wallet_account', $search_filter_wallet);
        }
        if (! empty($search_filter_payment_credit)) {
            $transactions = $transactions->where('payment_account', $search_filter_payment_credit);
        }

        if (! empty($search_transaction_account)) {
            if ($search_transaction_account == 1) {
                $transactions = $transactions->where('subaccount', '!=', null);
            } else {
                $transactions = $transactions->where('subaccount', null);
            }
        }
//         dd($transactions->toSql());

        return $transactions;
    }

    public static function updateTransaction($transactions, $request)
    {
        $account;
        if(!empty($request->subaccount)) {
            $account = Account::where('id', $request->subaccount)->first();
        }

        foreach ($transactions as $transaction) {
            $transaction->bank_account = ! empty($request->bank_account) ? $request->bank_account : $transaction->bank_account;
            $transaction->account_type = ! empty($request->account_type) ? $request->account_type : $transaction->account_type;
            $transaction->transaction_account_type = !empty($account) ? $account->account_type : '';
            $transaction->subaccount = ! empty($request->subaccount) ? $request->subaccount : $transaction->subaccount;
            $transaction->wallet_account = ! empty($request->edit_wallet_account) ? $request->edit_wallet_account : $transaction->wallet_account;
            $transaction->payment_account = ! empty($request->edit_payment_credit) ? $request->edit_payment_credit : $transaction->payment_account;
            $transaction->update();
        }
    }

    public function exportTransactions(Request $request)
    {
        $saveCron = Cron::create([
            'bank_account' => $request->bank_account,
            'sub_account' => $request->subAccount
        ]);

        return response()->json([
            'cronId' => $saveCron->id
        ]);
    }

    public function updateExportProgressBar(Request $request)
    {
        $cronId = $request->query('cronId');
        if ($cronId != null) {
            $cron = Cron::where('id', $cronId)->first();
            if ($cron) {
                if ($cron->total_records > 0) {
                    $progress = ($cron->updated_records / $cron->total_records) * 100;
                    $fileName = $cron->file_name;
                    $status = 1;
                    $cronId = $cron->id;
                } else {
                    $status = 0;
                    $fileName = '';
                    $progress = 0;
                    $cronId = 0;
                }
            } else {
                $status = 0;
                $fileName = '';
                $progress = 0;
                $cronId = 0;
            }
        } else {
            $cron = Cron::where('status', 0)->first();
            if ($cron) {
                if ($cron->total_records > 0) {
                    $progress = ($cron->updated_records / $cron->total_records) * 100;
                    $fileName = $cron->file_name;
                    $status = 1;
                    $cronId = $cron->id;
                } else {
                    $status = 0;
                    $fileName = '';
                    $progress = 0;
                    $cronId = 0;
                }
            } else {
                $status = 0;
                $fileName = '';
                $progress = 0;
                $cronId = 0;
            }
        }

        return response()->json([
            'success' => $status,
            'progress' => $progress,
            'fileName' => $fileName,
            'cronId' => $cronId
        ]);
    }

    public function saveInlineTransaction(Request $request)
    {
        $account = Account::where('id', $request->account)->first();
        $transaction = Transaction::where('id', $request->id)->update([
            'date' => $request->date,
            'amount' => $request->amount,
            'bank_account' => $request->bankAccount,
            'bank_description' => $request->bankDescription,
            'our_description' => $request->ourDescription,
            'subaccount' => $request->account,
            'transaction_account_type' => $account ? $account->account_type : ''
        ]);

        Session::flash('success', 'Transaction updated successfully.');

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully.'
        ]);
    }

    public function deleteAllTransactions()
    {
        $transactions = Transaction::where('status', 1)->update([
            'status' => 2
        ]);
        return redirect('/transaction')->with('info', 'All Transaction deleted successfully');
    }

    public function replaceAccountOptions() {
        $accountOptions = Account::where('status', Account::STATE_ACTIVE)->whereNull('parent_account_name')->get();
        $latestAccountAdded = Account::latest()->first();

        $html = '<option value="">Select Account sub account</option>';
        foreach($accountOptions as $account) {
            $html .= '<optgroup label="'.$account->name.'">';
            foreach($account->getSubAccount() as $subAccount) {
                $selected = $subAccount->id == $latestAccountAdded->id ? ' selected' : '';
                $html .= '<option value="'.$subAccount->id.'"'.$selected.'>'.$subAccount->name.'</option>';
            }
            $html .= '</optgroup>';
        }

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function addRuleTransaction(Request $request) {
        $validator = Validator::make($request->all(), [
            'account' => 'required',
            'rule_account_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $description_type = [
            'bank_description',
            'our_description'
        ];

        $criteriaJson = [];
        foreach($description_type as $key => $value){
            $description = $moreValues[$key] = $value;
            $criteriaJson['description'][] = $description;
            $criteriaJson['condition'][] = 'like';
            $criteriaJson['value'][] = $request->criteria_value;
        }

        $transactionRule = TransactionRule::create([
            'rule_name' => $request->rule_name,
            'account_type' => $request->rule_account_type,
            'transactions_category_type' => 'anyone_matching_criteria',
            'account_name' => $request->account,
            'wallet_account' => isset($request->rule_wallet_account) ? $request->rule_wallet_account : '',
            'payment_account' => isset($request->rule_payment_gateway) ? $request->rule_payment_gateway : '',
            'criteria' => $criteriaJson,
        ]);

        if($transactionRule) {
            $jsonResponse = [
                'success' => true,
                'message' => 'Rule added successfully.'
            ];
        } else {
            $jsonResponse = [
                'success' => false,
                'message' => 'Rule not added.'
            ];
        }

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

            if($transactions) {
                foreach($transactions->get() as $transaction)
                {
                    $suggest_rule = [$transactionRule->id];
                    $transaction->suggest_rule = $suggest_rule;
                    $transaction->update();
                }
            }

            $transactionRule->type = 1;
            $transactionRule->save();
        }

        return response()->json($jsonResponse);

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
}
