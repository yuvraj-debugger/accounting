<?php

namespace App\Console\Commands;

use App\Models\Cron;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class ExportTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cron = Cron::where('status', 0)->first();
        if($cron) {
            self::exportTransactions($cron);
            $cron->status = 1;
            $cron->save();
        }
    }

    public static function exportTransactions($cron) {
        // Define base query to fetch transactions
        $query = Transaction::where('status', 1)->orderBy('date', 'desc');

        // Apply filters if bank account or subAccount is provided
        if (!empty($cron->bank_account)) {
            $query->where('bank_account', $cron->bank_account);
        }

        if (!empty($cron->sub_account)) {
            $query->where('subaccount', $cron->sub_account);
        }

        // Fetch transactions data
        $transactions = $query->get();
        $cron->total_records = count($transactions);


        // Fetch bank account(s) data
        if(empty($cron->bank_account)) {
            $bankAccounts = BankAccount::where('status', 1)->get();

        } else {
            $bankAccounts = BankAccount::where('id', $cron->bank_account)->get();
        }

        // Set the filename for CSV
        $filename = 'bank_transactions_'.$cron->id.'.csv';
        $filePath = 'exports/' . $filename;


        // Create a new file or overwrite the existing one
        Storage::disk('public_uploads')->put($filePath, '');

        $handle = fopen(Storage::disk('public_uploads')->path($filePath), 'w');

        // Add CSV header
        fputcsv($handle, [
            'Opening Balance',
            'Date',
            'Debit',
            'Credit',
            'Balance',
            'Our Description',
            'Bank Description',
        ]);

        // Process transactions
        foreach ($transactions as $transaction) {
            // Find the bank account for the current transaction
            $bank = $bankAccounts->firstWhere('id', $transaction->bank_account);

            // Get opening balance and balance for the current bank account
            $openingBalance = $bank ? $bank->opening_balance : 0;
            $balance = $bank ? $bank->getBalance() : 0;

            // Add transaction data to CSV
            fputcsv($handle, [
                $openingBalance,
                date('d/m/y', strtotime($transaction->date)),
                $transaction->account_type == 'debit' ? $transaction->amount : '',
                $transaction->account_type == 'credit' ? $transaction->amount : '',
                $balance,
                $transaction->our_description,
                $transaction->bank_description,
            ]);

            $cron->updated_records = $cron->updated_records + 1;
            $cron->save();
        }

        $cron->file_name = $filePath;
        $cron->save();

        fclose($handle);
    }
}
