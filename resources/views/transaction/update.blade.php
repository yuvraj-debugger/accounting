<x-main> <!-- end of navbar navigation -->
    <div class="content">
        <div class="container">
            <div class="page-title">
                <h3>Update Transaction</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">Update Transaction</div>
                        <div class="card-body">
                            <form accept-charset="utf-8" action="{{ route('transaction.storeupdate', $transaction->id) }}"
                                method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Date</label> <input type="date"
                                        name="date" placeholder="Date" class="form-control"
                                        value="{{ $transaction->date }}"> @error('date')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Account Type</label>
                                    <select name="account_type" placeholder="Account Type" id="account_type"
                                        class="form-control all-select2_dropdown">
                                        <option value="">Select Account Type</option>
                                        <option value="debit"
                                            {{ $transaction->account_type == 'debit' ? 'selected' : '' }}>Debit</option>
                                        <option value="credit"
                                            {{ $transaction->account_type == 'credit' ? 'selected' : '' }}>Credit</option>

                                    </select>
                                    @error('account_type')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3" style="display: none;" id="bank_account_show">
                                    <label for="bank_account" class="form-label">Bank account</label> <br />
                                    <select name="bank_account" placeholder="Select Bank account"
                                        class="form-control all-select2_dropdown">
                                        <option value="">Select Bank account</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{ $bankAccount->id }}"
                                                {{ $transaction->bank_account == $bankAccount->id ? 'selected' : '' }}>
                                                {{ $bankAccount->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="mb-3" style="display: none;" id="wallet_account_show">
                                    <label for="bank_account" class="form-label">Wallet Account</label> <br />
                                    <select name="wallet_account" placeholder="Select Wallet account"
                                        class="form-control all-select2_dropdown ">
                                        <option value="">Select Wallet account</option>
                                        @foreach ($walletAccounts as $walletAccount)
                                            <option value="{{ $walletAccount->id }}"
                                                {{ $transaction->wallet_account == $walletAccount->id ? 'selected' : '' }}>
                                                {{ $walletAccount->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="mb-3" style="display: none;" id="payment_account_show">
                                    <label for="bank_account" class="form-label">Payment gateway Account</label> <br />
                                    <select name="payment_credit" placeholder="Select Payment Gateway Account"
                                        class="form-control all-select2_dropdown">
                                        <option value="">Select Payment gateway Account</option>
                                        @foreach ($paymentWallets as $paymentWallet)
                                            <option value="{{ $paymentWallet->id }}"
                                                {{ $transaction->payment_account == $paymentWallet->id ? 'selected' : '' }}>
                                                {{ $paymentWallet->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Bank Description</label>
                                    <textarea class="form-control" id="bank_description" name="bank_description" rows="4" cols="50">{{ $transaction->bank_description }}</textarea>
                                    @error('bank_description')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Our Description</label>
                                    <textarea class="form-control" id="our_description" name="our_description" rows="4" cols="50">{{ $transaction->our_description }}</textarea>
                                    @error('our_description')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Amount</label>
                                    <input type="number" name="amount" placeholder="Amount" class="form-control"
                                        value="{{ str_replace(',','',$transaction->amount) }}">
                                    @error('amount')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Account</label>
                                    <select name="subaccount" placeholder="Select Account sub account"
                                        class="form-control all-select2_dropdown">
                                        <option value="">Select Account sub account</option>
                                        @foreach ($subAccounts as $subAccount)
                                            <option value="{{ $subAccount->id }}"
                                                {{ $transaction->subaccount == $subAccount->id ? 'selected' : '' }}>
                                                {{ $subAccount->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="mb-3 row">
                                    <div class="col-sm-4 offset-sm-2">
                                        <a href="{{ route('transaction.index') }}" class="btn btn-secondary mb-2"><i
                                                class="fas fa-times"></i>
                                            Cancel</a>
                                        <button type="submit" class="btn btn-primary mb-2">
                                            <i class="fas fa-save"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .error {
            color: red;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>

    <script>
        $(document).ready(function() {
            $('.all-select2_dropdown').select2();

            var id = $(this).val();
            $("#bank_account_show").show();
            if ($('#account_type option:selected').val() == "credit") {
                $("#wallet_account_show").show().trigger('change');
                $("#payment_account_show").show();
            }
            $("#account_type").on('change', function() {
                var id = $(this).val();
                $("#bank_account_show").show();
                if (id == "credit") {
                    $("#wallet_account_show").show();
                    $("#payment_account_show").show();
                } else {
                    $("#wallet_account_show").hide();
                    $("#payment_account_show").hide();
                }

            });

        });
    </script>
</x-main>
