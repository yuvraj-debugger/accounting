<?php
use Illuminate\Support\Facades\Session;

?>
<x-main>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 50% ! important;
        }

        .loader {
            position: fixed;
            z-index: 1233;
            width: 100vw;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            top: 0%;
            background-color: #e0e0e094;
        }

        .save_data {
            background-color: #31d300;
            visibility: visible;
            cursor: pointer;
            border-radius: 7px;
            padding: 7px 16px 7px 16px;

            display: inline-block;
        }

        .inputsearch {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .transactionfilter button#filters {
            margin-bottom: 2px
        }

        .inputsearch input {
            max-width: 220px
        }

        .inputsearch select {
            max-width: 35px
        }

        .transactionfilter {
            display: flex;
            align-items: end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .transactionfilter .form-label {
            font-size: 16px;
            font-weight: 600;
        }

        .transactionfilter .select2-container .select2-selection--single {
            height: 42px;
        }

        .transactionfilter .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            width: 221px;
        }

        .transactionfilter .select2-container,
        .inline-select-span .select2-container {
            width: 100% !important;
        }

        .transactionfilter .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .transactionfilter .select2-container--default .select2-selection--single {
            border-radius: 5px;
            border-color: #dee2e6
        }

        .transactionfilter #reportrange i.fa {
            padding: 7.1px;
        }

        .transactionfilter #reportrange {
            border-radius: 5px;
            border: 1px solid #dee2e6 !important;
        }

        .loader .loaderInner {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader .loaderInner img {
            width: 73px
        }

        .fa {
            font-family: "FontAwesome";
            font-weight: 900;
            padding: 1rem 2rem;
            color: #3a9c7d;
        }

        .transactionoptions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px
        }

        .transactionoptions select {
            min-width: 100px;
            max-width: 180px;
        }
    </style>
    <!-- end of navbar navigation -->

    <div class="content">
        <div class="container" style="max-width: 100%">
            <div class="page-title">
                <h3>Transactions</h3>
            </div>
            <div class="loader" id="loader" style="display:none">
                <div class="loaderInner">
                    <img src="{{ asset('images/loading2.gif') }}">
                </div>
            </div>
            <div class="row">
                @if (Session::has('message'))
                    <div class="alert alert-success" id="message" role="alert">
                        {{ Session::get('message') }}
                    </div>
                @endif
                @if (Session::has('success'))
                    <div class="alert alert-success" id="success" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if (Session::has('info'))
                    <div class="alert alert-danger" id="info" role="alert">
                        {{ Session::get('info') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 col-lg-12 mb-2">

                        <form method="get" enctype="multipart/form-data">
                            <div class="transactionfilter">


                                <input type="hidden" id="search_transaction" name="search_transaction"
                                    value="{{ isset($_GET['search_transaction']) ? $_GET['search_transaction'] : '' }}" />
                                <input type="hidden" name="per_page" id="per_name_value"
                                    value="{{ isset($_GET['per_page']) ? $_GET['per_page'] : '' }}" />

                                <div>
                                    <label for="bank_account" class="form-label">Bank account</label><br />
                                    <select name="bank_account" placeholder="Select Bank account" class="form-control"
                                        id="filter_bank_account">
                                        <option value="">Select Bank account</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{ $bankAccount->id }}"
                                                {{ (isset($_GET['bank_account']) ? $_GET['bank_account'] : '') == $bankAccount->id ? 'selected' : '' }}>
                                                {{ $bankAccount->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="account_name" class="form-label">Account Type</label><br />
                                    <select name="account_type" placeholder="Account Type" class="form-control"
                                        id="account_type">
                                        <option value="">Select Account Type</option>
                                        <option value="debit"
                                            {{ (isset($_GET['account_type']) ? $_GET['account_type'] : '') == 'debit' ? 'selected' : '' }}>
                                            Debit</option>
                                        <option value="credit"
                                            {{ (isset($_GET['account_type']) ? $_GET['account_type'] : '') == 'credit' ? 'selected' : '' }}>
                                            Credit</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="account_name" class="form-label">Account</label><br />
                                    <select name="subaccount" placeholder="Select Account sub account" id="subaccount"
                                        class="form-control">
                                        <option value="">Select Account sub account</option>
                                        @foreach ($mainAccounts as $mainAccount)
                                            <optgroup label="{{ $mainAccount->name }}">
                                                @foreach ($mainAccount->getSubAccount() as $subAccount)
                                                    <option value="{{ $subAccount->id }}"
                                                        {{ (isset($_GET['subaccount']) ? $_GET['subaccount'] : '') == $subAccount->id ? 'selected' : '' }}>
                                                        {{ $subAccount->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach

                                    </select>
                                </div>

                                <div style="display: none;" id="filter_wallet_account_show">
                                    <label for="bank_account" class="form-label">Wallet Account</label><br />
                                    <select name="filter_wallet_account" placeholder="Select Wallet account"
                                        class="form-control " id="filter_wallet_account">
                                        <option value="">Select Wallet account</option>
                                        @foreach ($walletAccounts as $walletAccount)
                                            <option value="{{ $walletAccount->id }}"
                                                {{ old('wallet_account') == $walletAccount->id ? 'selected' : '' }}>
                                                {{ $walletAccount->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div style="display: none;" id="filter_payment_account_show">
                                    <label for="bank_account" class="form-label">Payment gateway Account</label><br />
                                    <select name="filter_payment_credit" placeholder="Select Payment Gateway Account"
                                        class="form-control " id="filter_payment_credit">
                                        <option value="">Select Payment gateway Account</option>
                                        @foreach ($paymentWallets as $paymentWallet)
                                            <option value="{{ $paymentWallet->id }}"
                                                {{ old('payment_credit') == $paymentWallet->id ? 'selected' : '' }}>
                                                {{ $paymentWallet->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div>
                                    <label class="form-label">Date</label>

                                    <div id="reportrange"
                                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <input type="hidden" name="custom_date" id="custom_date" value="" />
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span>{{ isset($_GET['custom_date']) ? $_GET['custom_date'] : '' }}</span> <i
                                            class="fa fa-caret-down"></i>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Have Account</label>
                                    <br />
                                    <select class="form-control" name="transaction_account"
                                        id="transaction_account_filter">
                                        <option value="">Select account</option>
                                        <option value="1"
                                            {{ (isset($_GET['transaction_account']) ? $_GET['transaction_account'] : '') == '1' ? 'selected' : '' }}>
                                            Have account</option>
                                        <option value="2"
                                            {{ (isset($_GET['transaction_account']) ? $_GET['transaction_account'] : '') == '2' ? 'selected' : '' }}>
                                            Have no account</option>
                                    </select>

                                </div>



                                <button type="submit" class="btn btn-primary" id="filters">Filter</button>

                        </form>
                    </div>


                    <div class="row align-items-center my-4">
                        <div class="col-lg-7">
                            <div class="transactionoptions">
                                <form method="GET">
                                    <input type="hidden" name="bank_account"
                                        value="{{ isset($_GET['bank_account']) ? $_GET['bank_account'] : '' }}" />
                                    <input type="hidden" name="account_type"
                                        value="{{ isset($_GET['account_type']) ? $_GET['account_type'] : '' }}" />
                                    <input type="hidden" name="subaccount"
                                        value="{{ isset($_GET['subaccount']) ? $_GET['subaccount'] : '' }}" />
                                    <input type="hidden" name="filter_wallet_account"
                                        value="{{ isset($_GET['filter_wallet_account']) ? $_GET['filter_wallet_account'] : '' }}" />
                                    <input type="hidden" name="filter_payment_credit"
                                        value="{{ isset($_GET['filter_payment_credit']) ? $_GET['filter_payment_credit'] : '' }}" />
                                    <input type="hidden" name="custom_date"
                                        value="{{ isset($_GET['custom_date']) ? $_GET['custom_date'] : '' }}" />
                                    <input type="hidden" name="transaction_account"
                                        value="{{ isset($_GET['transaction_account']) ? $_GET['transaction_account'] : '' }}" />


                                    <input type="hidden" id="search_transaction" name="search_transaction"
                                        value="{{ isset($_GET['search_transaction']) ? $_GET['search_transaction'] : '' }}" />

                                    <select name="per_page" id="per_page" class="form-control"
                                        onchange="this.form.submit()">
                                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20
                                        </option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                        </option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                        </option>
                                    </select>
                                </form>
                                <a href="{{ route('transaction.create') }}" class="btn btn-primary">
                                    <span class="fa-fw select-all fas"></span>
                                    Create
                                </a>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#importModal">
                                    Import
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    Export
                                </button>

                                <input type="hidden" id="exportCronId" value="" />

                                <a href="javascript:void(0);" class="btn btn-danger" id="deleteAll"
                                    style="display: none;">Delete (<span id="deleteRecordCount"></span>)
                                </a>

                                <a href="javascript:void(0);" class="btn btn-danger" id="deleteAllData"
                                    style="display: none;">
                                    Delete All
                                </a>

                                <button style="display: none;" type="button" id="bulk_edit_button"
                                    class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulk_modal">
                                    Bulk Edit (<span id="editRecordCount"></span>)
                                </button>

                                <a href="javascript:void(0);" style="display: none;" type="button"
                                    id="bulk_edit_data_button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#bulk_modal">
                                    Bulk Edit All
                                </a>
                            </div>

                        </div>
                        <div class="col-lg-5">
                            <div class="d-flex align-items-center flex-wrap justify-content-end">
                                <form method="get">
                                    <div>
                                        <input type="hidden" name="bank_account"
                                            value="{{ isset($_GET['bank_account']) ? $_GET['bank_account'] : '' }}" />
                                        <input type="hidden" name="account_type"
                                            value="{{ isset($_GET['account_type']) ? $_GET['account_type'] : '' }}" />
                                        <input type="hidden" name="subaccount"
                                            value="{{ isset($_GET['subaccount']) ? $_GET['subaccount'] : '' }}" />
                                        <input type="hidden" name="filter_wallet_account"
                                            value="{{ isset($_GET['filter_wallet_account']) ? $_GET['filter_wallet_account'] : '' }}" />
                                        <input type="hidden" name="filter_payment_credit"
                                            value="{{ isset($_GET['filter_payment_credit']) ? $_GET['filter_payment_credit'] : '' }}" />
                                        <input type="hidden" name="custom_date"
                                            value="{{ isset($_GET['custom_date']) ? $_GET['custom_date'] : '' }}" />
                                        <input type="hidden" name="transaction_account"
                                            value="{{ isset($_GET['transaction_account']) ? $_GET['transaction_account'] : '' }}" />



                                        {{-- Search Transaction Input start --}}
                                        <div class="inputsearch my-2">
                                            {{-- Add as Rule Button start --}}
                                            <div>
                                                @if (isset($_GET['search_transaction']))
                                                    @if ($_GET['search_transaction'] != '')
                                                        <button type="button" class="btn btn-primary me-1"
                                                            data-bs-toggle="modal" data-bs-target="#addRuleModal">
                                                            <span class="fa-fw select-all fas"></span> Create Rule
                                                        </button>
                                                    @endif
                                                @endif

                                            </div>
                                            {{-- Add as Rule Button end --}}
                                            <input type="search" id="search" name="search_transaction"
                                                value="{{ $search }}" class="form-control"
                                                placeholder="Search transaction..." />
                                            <select name="search_matching" id="search_matching" class="form-control"
                                                style="margin-left: 4px; margin-right: 4px;">
                                                <option value="1"
                                                    {{ isset($_GET['search_matching']) ? ($_GET['search_matching'] == 1 ? 'selected' : '') : '' }}
                                                    class="fa">
                                                    =
                                                </option>
                                                <option value="0"
                                                    {{ isset($_GET['search_matching']) ? ($_GET['search_matching'] == 0 ? 'selected' : '') : '' }}>
                                                    !=</option>
                                            </select>
                                            <input type="hidden" id="per_page" name="per_page"
                                                value="{{ isset($_GET['per_page']) ? $_GET['per_page'] : '' }}" />
                                            <button type="submit" class="btn btn-primary "
                                                style="margin-right: 2px">Search</button>
                                            <a href="/transaction" class="btn btn-primary">Reset</a>
                                        </div>
                                        {{-- Search Transaction Input end --}}
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>




                    <div class="col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">Transactions</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input select_all_ids" type="checkbox"
                                                            value="" id="selectAllRecords" />
                                                    </div>
                                                </th>
                                                <th>Date</th>
                                                <th>Bank Account</th>
                                                <th>Bank Description</th>
                                                <th>Description</th>
                                                <th>Account</th>
                                                <th>Amount</th>
                                                <th>Fixed rule</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="transanction-table-body">
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td style="width: 3%">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input userDelete checkbox_ids"
                                                                name="single_ids" type="checkbox"
                                                                value="{{ $transaction->id }}" id="flexCheckDefault"
                                                                onclick="checkbox()">
                                                        </div>
                                                    </td>

                                                    <td scope="row" style="width: 10%">
                                                        {{-- Editable date start --}}
                                                        <span id="date_{{ $transaction->id }}" class="date_input"
                                                            style="display: none;">
                                                            <input type="date" class="form-control"
                                                                value="{{ $transaction->date }}"
                                                                id="date_value_{{ $transaction->id }}" />
                                                        </span>
                                                        {{-- Editable date end --}}

                                                        <span onclick="editableRows('{{ $transaction->id }}')"
                                                            id="date_function_{{ $transaction->id }}"
                                                            class="date_input_function"
                                                            data-date="{{ $transaction->date }}">
                                                            {{ date('d M, Y', strtotime($transaction->date)) }}
                                                        </span>
                                                    </td>

                                                    <td style="width: 10%">
                                                        {{-- Editable bankAccount start --}}
                                                        <span id="bank_account_{{ $transaction->id }}"
                                                            class="date_input inline-select-span"
                                                            style="display: none;">
                                                            <select id="bankaccount_{{ $transaction->id }}"
                                                                class="form-control inline-select"
                                                                style="width: 200px !important;font-size:12px !important;">
                                                                @foreach ($bankAccounts as $bankAccount)
                                                                    <option value="{{ $bankAccount->id }}">
                                                                        {{ $bankAccount->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </span>
                                                        {{-- Editable bankAccount end --}}

                                                        <span onclick="editableRows('{{ $transaction->id }}')"
                                                            id="bank_account_function_{{ $transaction->id }}"
                                                            class="bank_account_input_function"
                                                            data-bankaccount="{{ $transaction->bank_account }}">
                                                            {{ !empty($transaction->getBankAccount()) ? $transaction->getBankAccount() : '' }}
                                                        </span>
                                                    </td>

                                                    <td style="width: 10%">
                                                        <span id="bankdescription_{{ $transaction->id }}"
                                                            class="bankdescription_input" style="display: none;">
                                                            <input type="text"
                                                                id="bank_description_{{ $transaction->id }}"
                                                                class="form-control" />
                                                        </span>
                                                        <span onclick="editableRows('{{ $transaction->id }}')"
                                                            id="bankdescription_function_{{ $transaction->id }}"
                                                            class="bankdescription_input_function"
                                                            data-bankdescription="{{ $transaction->bank_description }}">
                                                            {{ $transaction->bank_description }}
                                                        </span>
                                                        {{-- {{ $transaction->bank_description }} --}}
                                                    </td>

                                                    <td style="width: 10%">
                                                        <span id="ourdescription_{{ $transaction->id }}"
                                                            class="ourdescription_input" style="display: none;">
                                                            <input type="text" class="form-control"
                                                                id="our_description_{{ $transaction->id }}"
                                                                value="{{ $transaction->our_description }}" />
                                                        </span>
                                                        <span onclick="editableRows('{{ $transaction->id }}')"
                                                            id="ourdescription_function_{{ $transaction->id }}"
                                                            class="ourdescription_input_function"
                                                            data-our_description="{{ $transaction->our_description }}">
                                                            {{ @$transaction->our_description }}
                                                        </span>
                                                        {{-- {{ $transaction->our_description }} --}}
                                                    </td>

                                                    <td style="width: 10%">
                                                        <span id="sub_account_{{ $transaction->id }}"
                                                            class="subaccount_input inline-select-span"
                                                            style="display: none;">
                                                            <select id="subaccount_{{ $transaction->id }}"
                                                                class="form-control inline-select"
                                                                style="width: 200px !important;font-size:12px !important;">
                                                                <option>Select Account</option>
                                                                @foreach ($mainAccounts as $mainAccount)
                                                                    <optgroup label="{{ $mainAccount->name }}">
                                                                        @foreach ($mainAccount->getSubAccount() as $subAccount)
                                                                            <option value="{{ $subAccount->id }}">
                                                                                {{ $subAccount->name }}</option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                        </span>
                                                        <span onclick="editableRows('{{ $transaction->id }}')"
                                                            id="subaccount_function_{{ $transaction->id }}"
                                                            class="subaccount_input_function"
                                                            data-subaccount="{{ $transaction->subaccount }}">
                                                            {{ $transaction->getAccount() }}
                                                        </span>
                                                        {{-- {{ $transaction->getAccount() }} --}}
                                                    </td>

                                                    <td style="width: 10%">
                                                        <span id="amount_{{ $transaction->id }}" class="amount_input"
                                                            style="display: none;">
                                                            <input type="text" class="form-control"
                                                                id="amount_value_{{ $transaction->id }}" />
                                                        </span>
                                                        <span onclick="editableRows('{{ $transaction->id }}')"
                                                            id="amount_function_{{ $transaction->id }}"
                                                            class="amount_input_function"
                                                            data-amount="{{ @$transaction->amount }}">
                                                            {{ $transaction->account_type == 'debit' ? '-' : '+' }}
                                                            &#8377; {{ @$transaction->amount }}
                                                        </span>
                                                        {{-- {{ $transaction->amount }} --}}
                                                    </td>

                                                    <?php
                                                    $suggestAccountName = '';
                                                    if ($transaction->suggest_rule != null) {
                                                        $suggestRule = str_replace(['[', ']'], '', $transaction->suggest_rule);
                                                        $suggestAccountId = $transaction->getRuleAccountId($suggestRule);
                                                        $suggestAccountName = $transaction->getRuleAccountName($suggestAccountId);
                                                    }
                                                    ?>
                                                    <td style="width: 10%">{{ $suggestAccountName }}
                                                        @if ($transaction->subaccount == null && $transaction->suggest_rule != null)
                                                            <svg onclick="addSuggestedAccount({{ $transaction->id }},
                                                                {{ $transaction->getRuleAccountId(str_replace(['[', ']'], '', $transaction->suggest_rule)) }})"
                                                                style="width:20px; height:20px;" id="Layer_1"
                                                                style="enable-background:new 0 0 512 512;"
                                                                version="1.1" viewBox="0 0 512 512"
                                                                xml:space="preserve"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink">
                                                                <style type="text/css">
                                                                    .st0 {
                                                                        fill: #8DC542;
                                                                    }

                                                                    .st1 {
                                                                        fill: none;
                                                                        stroke: #FFFFFF;
                                                                        stroke-width: 30;
                                                                        stroke-miterlimit: 10;
                                                                    }
                                                                </style>
                                                                <path class="st0"
                                                                    d="M489,255.9c0-0.2,0-0.5,0-0.7c0-1.6,0-3.2-0.1-4.7c0-0.9-0.1-1.8-0.1-2.8c0-0.9-0.1-1.8-0.1-2.7  c-0.1-1.1-0.1-2.2-0.2-3.3c0-0.7-0.1-1.4-0.1-2.1c-0.1-1.2-0.2-2.4-0.3-3.6c0-0.5-0.1-1.1-0.1-1.6c-0.1-1.3-0.3-2.6-0.4-4  c0-0.3-0.1-0.7-0.1-1C474.3,113.2,375.7,22.9,256,22.9S37.7,113.2,24.5,229.5c0,0.3-0.1,0.7-0.1,1c-0.1,1.3-0.3,2.6-0.4,4  c-0.1,0.5-0.1,1.1-0.1,1.6c-0.1,1.2-0.2,2.4-0.3,3.6c0,0.7-0.1,1.4-0.1,2.1c-0.1,1.1-0.1,2.2-0.2,3.3c0,0.9-0.1,1.8-0.1,2.7  c0,0.9-0.1,1.8-0.1,2.8c0,1.6-0.1,3.2-0.1,4.7c0,0.2,0,0.5,0,0.7c0,0,0,0,0,0.1s0,0,0,0.1c0,0.2,0,0.5,0,0.7c0,1.6,0,3.2,0.1,4.7  c0,0.9,0.1,1.8,0.1,2.8c0,0.9,0.1,1.8,0.1,2.7c0.1,1.1,0.1,2.2,0.2,3.3c0,0.7,0.1,1.4,0.1,2.1c0.1,1.2,0.2,2.4,0.3,3.6  c0,0.5,0.1,1.1,0.1,1.6c0.1,1.3,0.3,2.6,0.4,4c0,0.3,0.1,0.7,0.1,1C37.7,398.8,136.3,489.1,256,489.1s218.3-90.3,231.5-206.5  c0-0.3,0.1-0.7,0.1-1c0.1-1.3,0.3-2.6,0.4-4c0.1-0.5,0.1-1.1,0.1-1.6c0.1-1.2,0.2-2.4,0.3-3.6c0-0.7,0.1-1.4,0.1-2.1  c0.1-1.1,0.1-2.2,0.2-3.3c0-0.9,0.1-1.8,0.1-2.7c0-0.9,0.1-1.8,0.1-2.8c0-1.6,0.1-3.2,0.1-4.7c0-0.2,0-0.5,0-0.7  C489,256,489,256,489,255.9C489,256,489,256,489,255.9z"
                                                                    id="XMLID_3_" />
                                                                <g id="XMLID_1_">
                                                                    <line class="st1" id="XMLID_2_"
                                                                        x1="213.6" x2="369.7" y1="344.2"
                                                                        y2="188.2" />
                                                                    <line class="st1" id="XMLID_4_"
                                                                        x1="233.8" x2="154.7" y1="345.2"
                                                                        y2="266.1" />
                                                                </g>
                                                            </svg>
                                                        @endif
                                                    </td>

                                                    <td style="width: 10%">
                                                        <span id="save_{{ $transaction->id }}" class="save_data"
                                                            onclick="saveInlineRowData('{{ $transaction->id }}')">
                                                            <i class="fa-solid fa-check" style="color:white"></i>
                                                        </span>

                                                        <a href="{{ route('transaction.update', $transaction->id) }}"
                                                            class="btn btn-success">
                                                            <span class="fa-fw select-all fas"></span>
                                                        </a>

                                                        <a href="{{ route('transaction.delete', $transaction->id) }}"
                                                            class="btn btn-danger">
                                                            <span class="fa-fw select-all fas"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $transactions->appends($query)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Bulk Edit Modal start --}}
    <div class="modal fade" id="bulk_modal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Bulk Edit Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bulk_edit_all">
                        @csrf
                        <input type="hidden" name="query" value="" id="query" />
                        <input type="hidden" id="all_edit_ids" name="all_edit_ids" value="" />

                        <div class="row">
                            <div class="col-md-12">
                                <label for="bank_account" class="form-label">Bank account</label>
                                <br />
                                <select name="bank_account" placeholder="Select Bank account" class="form-control"
                                    id="edit_all_bank_acount">
                                    <option value="">Select Bank account</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br />
                            <br />
                            <div class="col-md-12">
                                <label for="account_name" class="form-label">Account Type</label>
                                <br />
                                <select name="account_type" placeholder="Account Type" class="form-control"
                                    id="edit_all_account_type">
                                    <option value="">Select Account Type</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <br />
                            <br />
                            <div class="col-md-12">
                                <label for="account_name" class="form-label">Account</label><br />
                                <select name="subaccount" placeholder="Select Account sub account"
                                    class="form-control" id="edit_all_account">
                                    <option value="">Select Account sub account</option>
                                    @foreach ($mainAccounts as $mainAccount)
                                        <optgroup label="{{ $mainAccount->name }}">
                                            @foreach ($mainAccount->getSubAccount() as $subAccount)
                                                <option value="{{ $subAccount->id }}">
                                                    {{ $subAccount->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>


                            <div class="mb-3" style="display: none;" id="edit_wallet_account_show">
                                <label for="bank_account" class="form-label">Wallet Account</label><br />
                                <select name="edit_wallet_account" placeholder="Select Wallet account"
                                    class="form-control " id="bulk_edit_wallet_account">
                                    <option value="">Select Wallet account</option>
                                    @foreach ($walletAccounts as $walletAccount)
                                        <option value="{{ $walletAccount->id }}"
                                            {{ old('wallet_account') == $walletAccount->id ? 'selected' : '' }}>
                                            {{ $walletAccount->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="mb-3" style="display: none;" id="edit_payment_account_show">
                                <label for="bank_account" class="form-label">Payment gateway Account</label><br />
                                <select name="edit_payment_credit" placeholder="Select Payment Gateway Account"
                                    class="form-control " id="bulk_edit_payment_gateway">
                                    <option value="">Select Payment gateway Account</option>
                                    @foreach ($paymentWallets as $paymentWallet)
                                        <option value="{{ $paymentWallet->id }}"
                                            {{ old('payment_credit') == $paymentWallet->id ? 'selected' : '' }}>
                                            {{ $paymentWallet->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="d-flex justify-content-between" style="width: 100%">
                                <div>
                                    <button type="button" class="btn btn-primary" id="addAccount"
                                        data-bs-toggle="modal" data-bs-target="#addAccountModal">Add Account</button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Bulk Edit Modal end --}}


    {{-- Add New Account Modal start --}}
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Add Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success account-success" id="success" role="alert">
                        {{ Session::get('account-added') }}
                    </div>

                    <div class="alert alert-danger account-fail" id="info" role="alert">
                        {{ Session::get('account-not-added') }}
                    </div>

                    <form accept-charset="utf-8" id="addAccountForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" name="account_name" placeholder="Account Name"
                                class="form-control" value="{{ old('account_name') }}">
                            <span class="text-danger error" id="error_account_name"></span>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" placeholder="Description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="parent_account_name" class="form-label">Parent Account Name</label>
                            <select name="parent_account_name" placeholder="Account Type"
                                class="form-control all-select2_dropdown">
                                <option value="">Select Parent Account</option>
                                @foreach ($parentAccounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('parent_account_name') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="account_type" class="form-label">Account Type</label>
                            <select name="account_type" placeholder="Account Type"
                                class="form-control all-select2_dropdown">
                                <option value="">Select Account Type</option>
                                @foreach ($accountTypes as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('account_type') == $key ? 'selected' : '' }}>{{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error" id="error_account_type"></span>
                        </div>

                        <div class="mb-3">
                            <label for="opening_balance" class="form-label">Opening Balance</label>
                            <input type="text" name="opening_balance" placeholder="Opening Balance"
                                class="form-control"
                                value="{{ !empty(old('opening_balance')) ? old('opening_balance') : '' }}">
                        </div>

                        <div class="mb-3 row justify-content-between" style="width: 100%">
                            <div class="col-sm-4">
                                <a href="{{ route('account.index') }}" class="btn btn-secondary mb-2"
                                    data-bs-toggle="modal" data-bs-target="#bulk_modal"><i class="fas fa-times"></i>
                                    Cancel</a>

                            </div>
                            <div class="col-sm-4 d-flex justify-content-end">
                                <button type="button" id="saveAccount" class="btn btn-primary mb-2"><i
                                        class="fas fa-save"></i>
                                    Save</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Add New Account Modal end --}}


    {{-- Import Modal start --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Import Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" id="import_transaction">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    This is sample file <a href="{{ route('transaction.sampleImportFile') }}"
                                        style="cursor:pointer;"><span style="color: green">click here</span></a>
                                    <br />
                                    <label for="exampleFormControlInput1" class="">Import File:</label>
                                    <input type="file" class="form-control" name="file" accept=".csv">
                                </div>


                                <label for="bank_account" class="form-label">Bank account</label><br />
                                <select name="bank_account" placeholder="Select Bank account" class="form-control"
                                    id="importing_bank_account">
                                    <option value="">Select Bank account</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>

                    <hr />

                    <form method="post" action="{{ route('transaction.deleteImportedFile') }}">
                        @csrf
                        <h5 class="modal-title mb-2">Delete Imported Files</h5>
                        <div class="row">
                            <div class="col-md-12">

                                <select name="imported_file" placeholder="Select Bank account" class="form-control"
                                    id="imported_file">
                                    <option value="">Select Imported Files</option>
                                    @foreach ($importedFiles as $file)
                                        <option value="{{ $file->id }}">{{ $file->file_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger" id="filters">Delete</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Import Modal end --}}


    {{-- Export Modal start --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Export Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="progress">
                        <div class="progress mb-3" role="progressbar" aria-label="Animated striped example"
                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" id="progressMainDiv">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressDiv">
                            </div>
                        </div>
                    </div>
                    <form method="post" action="{{ route('transaction.exportTransactions') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label for="bank_account" class="form-label">Bank account</label><br />
                                <select name="bank_account" placeholder="Select Bank account" class="form-control"
                                    id="export_bank_account">
                                    <option value="">Select Bank account</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="subAccount" class="form-label">Account</label><br />
                                <select name="subAccount" placeholder="Select Account" class="form-control"
                                    id="export_sub_account">
                                    <option value="">Select Account</option>
                                    @foreach ($mainAccounts as $mainAccount)
                                        <optgroup label="{{ $mainAccount->name }}">
                                            @foreach ($mainAccount->getSubAccount() as $subAccount)
                                                <option value="{{ $subAccount->id }}">
                                                    {{ $subAccount->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="export">Export</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Export Modal end --}}


    {{-- Add Rule Modal start --}}
    <div class="modal fade" id="addRuleModal" tabindex="-1" aria-labelledby="addRuleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRuleModalLabel">Add Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success rule-success" id="success" role="alert"
                        style="display: none">
                        {{ Session::get('rule-added') }}
                    </div>

                    <div class="alert alert-danger rule-fail" id="info" role="alert" style="display: none">
                        {{ Session::get('rule-not-added') }}
                    </div>

                    <form method="post" id="addRuleTransaction"
                        action="{{ route('transaction.addRuleTransaction') }}">
                        @csrf
                        <input type="hidden" name="rule_name"
                            value="{{ isset($_GET['search_transaction']) ? $_GET['search_transaction'] : '' }}" />
                        <input type="hidden" name="criteria_value"
                            value="{{ isset($_GET['search_transaction']) ? $_GET['search_transaction'] : '' }}" />
                        <input type="hidden" name="query" id="transactionQuery" value="" />

                        <div class="row">
                            <div class="col-md-12">
                                <label for="account" class="form-label">Account</label><br />
                                <select name="account" placeholder="Select account" class="form-control"
                                    id="add_rule_account">
                                    <option value="">Select Account</option>
                                    @foreach ($mainAccounts as $mainAccount)
                                        <optgroup label="{{ $mainAccount->name }}">
                                            @foreach ($mainAccount->getSubAccount() as $subAccount)
                                                <option value="{{ $subAccount->id }}"
                                                    {{ (isset($_GET['subaccount']) ? $_GET['subaccount'] : '') == $subAccount->id ? 'selected' : '' }}>
                                                    {{ $subAccount->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <span class="text-danger error" id="error_account"></span>
                            </div>

                            <div class="col-md-12">
                                <label for="account" class="form-label">Account Type</label><br />
                                <select name="rule_account_type" class="form-control all-select2_dropdown"
                                    id="ruleAccountType">
                                    <option value="">Select Account Type</option>
                                    <option value="credit" {{ old('account_type') == 'debit' ? 'selected' : '' }}>
                                        Credit</option>
                                    <option value="debit" {{ old('account_type') == 'credit' ? 'selected' : '' }}>
                                        Debit</option>
                                </select>
                                <span class="text-danger error" id="error_rule_account_type"></span>
                            </div>
                            <div class="mb-3" style="display: none;" id="rule_wallet_account_show">
                                <label for="bank_account" class="form-label">Wallet Account</label><br />
                                <select name="rule_wallet_account" placeholder="Select Wallet account"
                                    class="form-control " id="rule_wallet_account">
                                    <option value="">Select Wallet account</option>
                                    @foreach ($walletAccounts as $walletAccount)
                                        <option value="{{ $walletAccount->id }}"
                                            {{ old('wallet_account') == $walletAccount->id ? 'selected' : '' }}>
                                            {{ $walletAccount->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="mb-3" style="display: none;" id="rule_payment_account_show">
                                <label for="bank_account" class="form-label">Payment gateway Account</label><br />
                                <select name="rule_payment_gateway" placeholder="Select Payment Gateway Account"
                                    class="form-control " id="rule_payment_gateway">
                                    <option value="">Select Payment gateway Account</option>
                                    @foreach ($paymentWallets as $paymentWallet)
                                        <option value="{{ $paymentWallet->id }}"
                                            {{ old('payment_credit') == $paymentWallet->id ? 'selected' : '' }}>
                                            {{ $paymentWallet->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="submitAddRule" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Add Rule Modal end --}}


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



    <script>
        var urlQueryParams = window.location.href.split('?')[1];
        $(document).ready(function() {
            /*** Initialize Select2 dropdowns start ***/
            $("#export_bank_account").select2({
                dropdownParent: $("#exportModal")
            });
            $("#importing_bank_account").select2({
                dropdownParent: $("#importModal")
            });

            $("#imported_file").select2({
                dropdownParent: $("#importModal")
            });

            $("#export_sub_account").select2({
                dropdownParent: $("#exportModal")
            });

            $("#bulk_edit_payment_gateway").select2({
                dropdownParent: $("#bulk_modal")
            });

            $("#bulk_edit_wallet_account").select2({
                dropdownParent: $("#bulk_modal")
            });

            $("#edit_all_bank_acount").select2({
                dropdownParent: $("#bulk_modal")
            });

            $("#edit_all_account_type").select2({
                dropdownParent: $("#bulk_modal")
            });

            $("#edit_all_account").select2({
                dropdownParent: $("#bulk_modal")
            });

            $("#add_rule_account").select2({
                dropdownParent: $("#addRuleModal")
            });

            $("#ruleAccountType").select2({
                dropdownParent: $("#addRuleModal")
            });

            $("#rule_wallet_account").select2({
                dropdownParent: $("#addRuleModal")
            });

            $("#rule_payment_gateway").select2({
                dropdownParent: $("#addRuleModal")
            });

            $("#filter_bank_account").select2();

            $("#filter_payment_credit").select2();

            $("#subaccount").select2();

            $("#filter_wallet_account").select2();

            $("#transaction_account_filter").select2();

            $('.inline-select').select2();
            /*** Initialize Select2 dropdowns end ***/


            $("#account_type").on('change', function(e) {
                var id = $(this).val();
                $("#bank_account_show").show();
                if (id == "credit") {
                    $("#filter_wallet_account_show").show();
                    $("#filter_payment_account_show").show();
                } else {
                    $("#filter_wallet_account_show").hide();
                    $("#filter_payment_account_show").hide();
                }

            });

            $("#account_type").select2();

            $("#ruleAccountType").on('change', function(e) {
                var id = $(this).val();
                if (id == "credit") {
                    $("#rule_wallet_account_show").show();
                    $("#rule_payment_account_show").show();
                } else {
                    $("#rule_wallet_account_show").hide();
                    $("#rule_payment_account_show").hide();
                }

            });
            $("#edit_all_account_type").on('change', function(e) {
                var id = $(this).val();
                $("#bank_account_show").show();
                if (id == "credit") {
                    $("#edit_wallet_account_show").show();
                    $("#edit_payment_account_show").show();
                } else {
                    $("#edit_wallet_account_show").hide();
                    $("#edit_payment_account_show").hide();
                }

            });

            $("#edit_all_account_type").on('select2:select', function(e) {
                var id = $(this).val();
                $("#bank_account_show").show();
                if (id == "credit") {
                    $("#edit_wallet_account_show").show();
                    $("#edit_payment_account_show").show();
                } else {
                    $("#edit_wallet_account_show").hide();
                    $("#edit_payment_account_show").hide();
                }

            });


            $("#account_type").on('select2:select', function(e) {
                var id = $(this).val();
                $("#bank_account_show").show();
                if (id == "credit") {
                    $("#filter_wallet_account_show").show();
                    $("#filter_payment_account_show").show();
                } else {
                    $("#filter_wallet_account_show").hide();
                    $("#filter_payment_account_show").hide();
                }

            });

            $(".save_data").css('visibility', 'hidden');
            $('#progressMainDiv').hide();

            // Export Transactions in CSV file
            $('#export').on('click', function() {
                $('#progressMainDiv').show();
                $('#export').prop('disabled', true);
                let data = {
                    bank_account: $('#export_bank_account').val(),
                    subAccount: $('#export_sub_account').val()
                };
                $.ajax({
                    url: "{{ route('transaction.exportTransactions') }}",
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        let cronId = response.cronId;
                        $('#exportCronId').val(cronId);
                    }
                })
            });

            // Split the query string into key-value pairs
            var pairs = urlQueryParams.split('&');

            // Iterate over each pair
            pairs.forEach(function(pair) {
                var keyValue = pair.split('=');
                var key = keyValue[0];
                var value = keyValue[1];
                $('#' + key + 'Query').val(value);
            });








            // Submit Add Rule from transaction
            $('#submitAddRule').on('click', function(e) {
                $('#transactionQuery').val(urlQueryParams);
                $('#loader').show();
                $.ajax({
                    url: "{{ route('transaction.addRuleTransaction') }}",
                    type: 'POST',
                    data: $('#addRuleTransaction').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if ($.isEmptyObject(response.errors)) {
                            $('#loader').hide();
                            if (response.success) {
                                $('.rule-success').css('display', 'block');
                                $('.rule-success').html(response.message);
                            } else {
                                $('.rule-fail').css('display', 'block');
                                $('.rule-fail').html(response.message);
                            }

                            setTimeout(function() {
                                $('.rule-success').css('display', 'none');
                                $('.rule-success').html('');
                                $('.rule-fail').css('display', 'none');
                                $('.rule-fail').html('');

                                $('#addRuleModal').modal('hide');
                                window.location.href = '/transaction?' + urlQueryParams;
                            }, 3000);

                        } else {
                            $('#loader').hide();
                            $.each(response.errors, function(key, value) {
                                console.log(key);
                                $('#error_' + key).html(value)
                            });
                        }
                    }
                });
            })









        });

        // Save Inline Transaction update
        $('#saveAccount').on('click', function() {
            $('#loader').show();
            let form = $('#addAccountForm').serialize();
            $.ajax({
                url: "{{ route('account.addAcccountFromTransactions') }}",
                type: 'POST',
                data: form,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loader').hide();
                    if ($.isEmptyObject(response.errors)) {
                        if (response.success) {
                            $('.account-success').css('display', 'block');
                            $('.account-success').html(response.message);
                        } else {
                            $('.account-fail').css('display', 'block');
                            $('.account-fail').html(response.message);
                        }

                        setTimeout(function() {
                            $('.account-success').css('display', 'none');
                            $('.account-success').html('');
                        }, 3000);

                        setTimeout(function() {
                            $('.account-fail').css('display', 'none');
                            $('.account-fail').html('');
                        }, 3000);

                        setTimeout(function() {
                            $('#addAccountModal').modal('hide');
                            $("#addAccountForm")[0].reset();
                            // Refresh account dropdown in bulk edit modal
                            replaceAccountOptionsInBulkEditModal();
                        }, 3000);

                    } else {
                        $.each(response.errors, function(key, value) {
                            $('#error_' + key).html(value)
                        });
                    }
                }
            });
        });


        function replaceAccountOptionsInBulkEditModal() {
            $.ajax({
                url: "{{ route('transaction.replaceAccountOptions') }}",
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    let accountOptionsHtml = response.html;
                    $('#edit_all_account').html(accountOptionsHtml);
                    $('#bulk_modal').modal('show');
                }
            })
        }


        $('#selectAllRecords').on('click', function() {
            if ($(this).is(':checked')) {
                showDeleteAllButtonAndCount('show', 'all');
            } else {
                showDeleteAllButtonAndCount('hide', 'all');
            }
        });


        $('#bulk_edit_button').click(function() {
            var edit_all_ids = [];
            $('input:checkbox[name="single_ids"]:checked').each(function() {
                edit_all_ids.push($(this).val());
            });
            $("#all_edit_ids").val(edit_all_ids.join(","));
        });

        $('#bulk_edit_data_button').click(function() {
            var edit_all_ids = [];
            $("#all_edit_ids").val(edit_all_ids.join(","));
            $("#query").val(urlQueryParams)
        });


        function showDeleteAllButtonAndCount(show, type) {
            if (show == 'show') {
                $('#deleteAll').show();
                $('#deleteAllData').show();
                $('#bulk_edit_button').show();
                $('#bulk_edit_data_button').show();

                if (type == 'all') {
                    $('.userDelete').prop('checked', true);
                    let recordCount = $('.userDelete').prop('checked', true).length;
                    $('#deleteRecordCount').html(recordCount);
                    $('#editRecordCount').html(recordCount);

                }

            } else {
                $('#deleteAll').hide();
                $('#deleteAllData').hide();
                $('#bulk_edit_button').hide();
                $('#bulk_edit_data_button').hide();


                if (type == 'all') {
                    $('.userDelete').prop('checked', false);
                }
            }
        }

        function checkbox() {
            if ($(this).prop('checked', false)) {
                $('#selectAllRecords').prop('checked', false);
            }
            var val = [];
            $('.checkbox_ids:checked').each(function(i) {
                val[i] = $(this).val();
            });
            $('#deleteRecordCount').text(val.length);
            $('#editRecordCount').text(val.length);

            if (val.length == 0) {
                $('#deleteAll').hide();
                $('#bulk_edit_button').hide();
            } else {
                $('#deleteAll').show();
                $('#bulk_edit_button').show();
            }
        }


        // Import Transactions from CSV file
        $('#import_transaction').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $('#loader').show()

            $.ajax({
                type: 'post',
                url: "{{ route('transaction.importTransactions') }}",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#loader').hide()
                    window.location.href = "/transaction";
                }
            });
        });


        $('#bulk_edit_all').on('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $('#loader').show()

            $.ajax({
                type: 'post',
                url: "{{ route('transaction.editAll') }}",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#loader').hide()
                    window.location.reload();
                }
            });
        });


        $('#deleteAll').click(function(e) {
            $('#loader').show()
            e.preventDefault();
            var all_ids = [];
            $('input:checkbox[name="single_ids"]:checked').each(function() {
                all_ids.push($(this).val());
            });

            $.ajax({
                type: 'post',
                url: "{{ route('transaction.transactionDeleteAll') }}",
                data: "all_ids=" + all_ids + "",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loader').hide()
                    window.location.href = "/transaction";
                }
            });
        });


        $('#deleteAllData').click(function(e) {
            $('#loader').show()
            e.preventDefault();
            var all_ids = [];
            let data = {
                'all_ids': all_ids,
                'query': urlQueryParams
            }
            $.ajax({
                type: 'post',
                url: "{{ route('transaction.transactionDeleteAll') }}",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loader').hide();
                    window.location.href = "/transaction";
                }
            });
        });

        setTimeout(function() {
            $('#success').hide();
            $('#info').hide();
        }, 3000);


        $(function() {

            <?php
                if(!isset($_GET['custom_date'])){
            ?>
            var start = [];
            var end = [];
            <?php
                }
                else
                {
            ?>
            var start = "{{ $_GET['custom_date'] }}";
            var end = "{{ $_GET['custom_date'] }}";
            <?php
                }
            ?>

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#custom_date').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });


        function addSuggestedAccount(transactionId, accountId) {
            let data = {
                'transactionId': transactionId,
                'accountId': accountId
            };

            $.ajax({
                type: 'POST',
                url: '{{ route('transaction.applySuggestedAccount') }}',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }

            });
        }
    </script>


    <script>
        function editableRows(id) {

            var date = $('#date_function_' + id).data('date');
            $('#date_value_' + id).val(date);

            var amount = $('#amount_function_' + id).data('amount');
            $('#amount_value_' + id).val(amount);

            var accountType = $('#accounttype_function_' + id).data('accounttype');

            var subaccount = $('#subaccount_function_' + id).data('subaccount');

            var fixed_rule = $('#fixed_rule_function_' + id).data('fixed_rule');


            var ourDescription = $('#ourdescription_function_' + id).text();
            $("#our_description_" + id).val(ourDescription.trim());

            var bankDescription = $('#bankdescription_function_' + id).text();
            $('#bank_description_' + id).val(bankDescription.trim());

            var bankaccount = $('#bank_account_function_' + id).data('bankaccount');

            $('#account_type_' + id).val(accountType);
            $('#account_type_' + id).trigger('change').prop('selected', true);

            $('#subaccount_' + id).val(subaccount);
            $('#subaccount_' + id).trigger('change').prop('selected', true);

            $('#fixedrule_' + id).val(fixed_rule);
            $('#fixedrule_' + id).trigger('change').prop('selected', true);

            $('#bankaccount_' + id).val(bankaccount);
            $('#bankaccount_' + id).trigger('change').prop('selected', true);

            $('.date_input').hide();
            $('.date_input_function').show();

            $('.accounttype_input').hide();
            $('.accounttype_input_function').show();

            $('.subaccount_input').hide();
            $('.subaccount_input_function').show();

            $('.ourdescription_input').hide();
            $('.ourdescription_input_function').show();

            $('.bankdescription_input').hide();
            $('.bankdescription_input_function').show();

            $('.bank_account_input').hide();
            $('.bank_account_input_function').show();

            $('.fixed_rule_input').hide();
            $('.fixed_rule_input_function').show();

            $('.amount_input').hide();
            $('.amount_input_function').show();

            $('.save_data').css('visibility', 'hidden');
            $('.edit_delete_data').show();

            $('#date_' + id).show();
            $('#date_function_' + id).hide();

            $('#accounttype_' + id).show();
            $('#accounttype_function_' + id).hide();

            $('#sub_account_' + id).show();
            $('#subaccount_function_' + id).hide();

            $('#fixed_rule_' + id).show();
            $('#fixed_rule_function_' + id).hide();

            $('#ourdescription_' + id).show();
            $('#ourdescription_function_' + id).hide();

            $('#bankdescription_' + id).show();
            $('#bankdescription_function_' + id).hide();

            $('#bank_account_' + id).show();
            $('#bank_account_function_' + id).hide();

            $('#amount_' + id).show();
            $('#amount_function_' + id).hide();

            $('#save_' + id).show();
            $("#save_" + id).css('visibility', 'visible');
            $('#editDelete_' + id).hide();
        }

        function saveInlineRowData(id) {
            let data = {
                'id': id,
                'date': $('#date_value_' + id).val(),
                'bankAccount': $('#bankaccount_' + id).val(),
                'bankDescription': $('#bank_description_' + id).val(),
                'ourDescription': $('#our_description_' + id).val(),
                'account': $('#subaccount_' + id).val(),
                'amount': $('#amount_value_' + id).val()
            };

            $.ajax({
                url: "{{ route('transaction.saveInlineTransaction') }}",
                type: "POST",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            })
        }

        function formatIndianCurrency(amount) {
            const x = amount.toString().split('.');
            let lastThree = x[0].substring(x[0].length - 3);
            const otherNumbers = x[0].substring(0, x[0].length - 3);
            if (otherNumbers != '') {
                lastThree = ',' + lastThree;
            }
            const res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
            return res + (x.length > 1 ? '.' + x[1] : '');
        }

        $(document).ready(function() {
            $('.amount_input_function').each(function() {
                const amount = parseFloat($(this).data('amount'));
                const accountType = $(this).data('type');
                const formattedAmount = formatIndianCurrency(amount.toFixed(2));
                $(this).html((accountType === 'debit' ? '-' : '+') + ' &#8377; ' + formattedAmount);
            });
        });
    </script>

    <script>
        // Progress Bar
        setInterval(function() {
            let cronId = $('#exportCronId').val();
            if (cronId == '') {
                route = "{{ route('transaction.updateExportProgress') }}"
            } else {
                route = "{{ route('transaction.updateExportProgress', ['cronId' => 'CRON_ID_PLACEHOLDER']) }}"
                    .replace('CRON_ID_PLACEHOLDER', cronId);
            }
            $.ajax({
                url: route,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        console.log(response);
                        if (response.cronId > 0) {
                            $('#exportCronId').val(response.cronId);
                        }

                        $('#export').prop('disabled', true);
                        let fileName = response.fileName;
                        let progress = Math.floor(response.progress);
                        $('#progressMainDiv').show();

                        if (progress == 0) {
                            $('#progressMainDiv').html(progress + '%');

                        } else {
                            $('#progressMainDiv').html(
                                '<div class="progress-bar progress-bar-striped progress-bar-animated" id="progressDiv"></div>'
                            );
                            $('#progressDiv').html(progress + '%');
                            $('#progressDiv').css('width', progress + '%');
                        }

                        if (response.fileName != null) {
                            $('#progressMainDiv').hide();
                            $('#export').prop('disabled', false);
                            $('#progress').html(
                                `<div class="alert alert-success">File Exported successfully, download it from here: <a href="{{ URL::to('${fileName}') }}" download><u><b>Your File</b></u></a></div>`
                            );
                        }
                    } else {
                        $('#progressMainDiv').hide();
                        $('#export').prop('disabled', false);
                        return false;
                    }
                }
            })
        }, 1000);
    </script>


</x-main>
