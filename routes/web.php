<?php
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionRuleController;
use App\Http\Controllers\TransactionReportController;
use App\Http\Controllers\TransactionSummaryController;

Route::any('/', function () {
    if (! Auth::user()) {
        return redirect('login');
    } else {
        return redirect('dashboard');
    }
});

Route::get('/register', [
    AuthController::class,
    'registerView'
])->name('register');
Route::post('/register', [
    AuthController::class,
    'register'
])->name('register');
Route::any('/forgot-password', [
    AuthController::class,
    'forgotPassword'
])->name('forgotPassword');
Route::any('/forgot-password-submit', [
    AuthController::class,
    'resetPasswordSubmit'
])->name('reset-password-submit');

Route::get('reset-password/{token}', [
    AuthController::class,
    'showResetPasswordForm'
])->name('reset.password.get');
Route::post('reset-password', [
    AuthController::class,
    'submitResetPasswordForm'
])->name('reset.password.post');

Route::get('/login', [
    AuthController::class,
    'index'
])->name('login');
Route::post('/login', [
    AuthController::class,
    'login'
])->name('login');

Route::group([
    'middleware' => 'auth'
], function () {

    Route::any('/dashboard', function () {
        return view('index');
    });
    Route::controller(AccountController::class)->prefix('account')
        ->as('account.')
        ->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/update/{id}', 'update')->name('update');
        Route::post('/storeupdate/{id}', 'storeupdate')->name('storeupdate');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::post('/accountDeleteAll', 'accountDeleteAll')->name('accountDeleteAll');
        Route::post('/add-account-transaction', 'addAcccountFromTransactions')->name('addAcccountFromTransactions');

    });

    Route::controller(BankController::class)->prefix('bank')
        ->as('bank.')
        ->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/update/{id}', 'update')->name('update');
        Route::post('/storeupdate/{id}', 'storeupdate')->name('storeupdate');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::post('/bankDeleteAll', 'bankDeleteAll')->name('bankDeleteAll');


    });

    Route::controller(TransactionController::class)->prefix('transaction')
        ->as('transaction.')
        ->group(function () {
        Route::any('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/update/{id}', 'update')->name('update');
        Route::post('/storeupdate/{id}', 'storeupdate')->name('storeupdate');
        Route::get('/delete/{id}', 'delete')->name('delete');
        Route::get('/search', 'search')->name('search');
        Route::any('/deleteAll', 'deleteAll')->name('transactionDeleteAll');
        Route::post('/accountTypeData', 'accountTypeData')->name('accountTypeData');
        Route::get('/sample-import-file', 'downloadSampleImportFile')->name('sampleImportFile');
        Route::post('/import', 'importTransactions')->name('importTransactions');
        Route::post('/apply-suggested-account', 'applySuggestedAccount')->name('applySuggestedAccount');
        Route::post('/filters', 'filters')->name('filters');
        Route::post('/delete-import-file', 'deleteImportedFile')->name('deleteImportedFile');
        Route::post('/edit-all', 'editAll')->name('editAll');
        Route::post('/export', 'exportTransactions')->name('exportTransactions');
        Route::post('/save-inline-transaction', 'saveInlineTransaction')->name('saveInlineTransaction');
        Route::get('/export-progress-update', 'updateExportProgressBar')->name('updateExportProgress');
        Route::get('/replace-account-options', 'replaceAccountOptions')->name('replaceAccountOptions');
        Route::post('/add-rule', 'addRuleTransaction')->name('addRuleTransaction');
    });

    Route::controller(TransactionRuleController::class)->prefix('transaction-rule')
        ->as('transaction-rule.')
        ->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/update/{id}', 'update')->name('update');
        Route::post('/storeupdate/{id}', 'storeupdate')->name('storeupdate');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });

    Route::controller(TransactionReportController::class)->prefix('transaction-report')
    ->as('transaction-report.')
    ->group(function() {
        Route::get('', 'index')->name('index');
    });

    Route::controller(TransactionSummaryController::class)->prefix('transaction-summary')
    ->as('transaction-summary.')
    ->group(function() {
        Route::get('', 'index')->name('index');
    });

    Route::get('/income-expense-report', function () {
        return view('income-expense-report');
    });

    Route::any('/logout', [
        AuthController::class,
        'logout'
    ])->name('logout');
    Route::get('/user-profile', [
        AuthController::class,
        'userProfile'
    ])->name('user-profile');
    Route::any('/profile-update', [
        AuthController::class,
        'profileUpdate'
    ])->name('profile-update');
    Route::any('/update-password', [
        AuthController::class,
        'updatePassword'
    ])->name('update-password');
});
