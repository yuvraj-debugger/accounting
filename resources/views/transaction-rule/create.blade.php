<x-main>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container">
            <div class="page-title">
                <h3>Create Transaction Rule</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">Create Transaction Rule</div>
                        <div class="card-body">
                            <form accept-charset="utf-8" action="{{ route('transaction-rule.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="rule_name" class="form-label">Rule Name</label>
                                    <input type="text" name="rule_name" placeholder="Rule Name" class="form-control"
                                        value="{{ old('rule_name') }}">
                                    @error('rule_name')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_type" class="form-label">Apply To:</label>
                                    <select type="text" name="account_type" class="form-control all-select2_dropdown" id="accountType">
                                        <option value="">Select Apply To</option>
                                        <option value="credit" {{ old('account_type') == 'debit' ? 'selected' : '' }}>Credit</option>
                                        <option value="debit" {{ old('account_type') == 'credit' ? 'selected' : '' }}>Debit</option>
                                    </select>

                                    @error('account_type')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="criteria" class="form-label">Categorize the transaction when:</label>
                                    <select type="text" name="criteria" placeholder="Rule Name" class="form-control all-select2_dropdown">
                                        <option value="">Select Criteria</option>
                                        <option value="all_matching_criteria" {{ old('criteria.0') == 'all_matching_criteria' ? 'selected' : '' }}>All the following criteria matches</option>
                                        <option value="anyone_matching_criteria" {{ old('criteria.0') == 'anyone_matching_criteria' ? 'selected' : '' }}>Any one of the following criteria matches</option>
                                    </select>

                                    @error('criteria')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 row" id="newinput">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <select class="form-control all-select2_dropdown" name="description_type[]">
                                                <option value="">Select Type</option>
                                                <option value="bank_description" {{ old('description_type.0') == 'bank_description' ? 'selected' : '' }}>Bank Description</option>
                                                <option value="our_description" {{ old('description_type.0') == 'our_description' ? 'selected' : '' }}>Our Description</option>
                                            </select>
                                            @error('description_type.0')
                                                <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-3">
                                            <select class="form-control all-select2_dropdown" name="like[]">
                                                <option value="">Select Like</option>
                                                <option value="like" {{ old('like.0') == 'like' ? 'selected' : '' }}>Like</option>
                                            </select>
                                            @error('like.0')
                                                <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="value" name="value[]" value="{{ old('value.0') }}">
                                            @error('value.0')
                                                <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-3">
                                            <a class="add-more" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-plus-circle-fill"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="account" class="form-label">Account</label>
                                    <select type="text" name="account" placeholder="Rule Name" class="form-control all-select2_dropdown">
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('account')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="walletAccountDiv">
                                    <label for="wallet_account" class="form-label">Wallet Account</label>
                                    <select type="text" name="wallet_account" placeholder="Rule Name"
                                        class="form-control all-select2_dropdown">
                                        <option value="">Select Wallet Account</option>
                                        @foreach ($wallets as $wallet)
                                            <option value="{{ $wallet->id }}" {{ old('wallet_account') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('wallet_account')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="paymentGatewayDiv">
                                    <label for="payment_account" class="form-label">Payment Gateway Account</label>
                                    <select type="text" name="payment_account" placeholder="Rule Name"
                                        class="form-control all-select2_dropdown">
                                        <option value="">Select Payment Gateway Account</option>
                                        @foreach ($paymentGateways as $paymentGateway)
                                            <option value="{{ $paymentGateway->id }}" {{ old('payment_account') == $paymentGateway->id ? 'selected' : '' }}>{{ $paymentGateway->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('payment_account')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 row">
                                    <div class="col-sm-4 offset-sm-2">
                                        <a href="{{ route('transaction-rule.index') }}"
                                            class="btn btn-secondary mb-2"><i class="fas fa-times"></i> Cancel</a>
                                        <button type="submit" class="btn btn-primary mb-2"><i
                                                class="fas fa-save"></i> Save</button>
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

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#walletAccountDiv').hide();
            $('#paymentGatewayDiv').hide();

            $(".add-more").click(function() {
                newRowAdd =
                    `<div class="row mt-2 row-div">
                    <div class="col-sm-3">
                        <select class="form-control all-select2_dropdown" name="description_type[]">
                            <option value="">Select Type</option>
                            <option value="bank_description">Bank Description</option>
                            <option value="our_description">Our Description</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control all-select2_dropdown" name="like[]">
                            <option value="">Select Like</option>
                            <option value="like">Like</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control    " id="value" name="value[]">
                    </div>
                    <div class="col-sm-3">
                        <a class="remove-more" type="button">
                            <svg width="16px" height="16px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                    <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-570.000000, -1089.000000)" fill="#d95656">
                                        <path d="M591.657,1109.24 C592.048,1109.63 592.048,1110.27 591.657,1110.66 C591.267,1111.05 590.633,1111.05 590.242,1110.66 L586.006,1106.42 L581.74,1110.69 C581.346,1111.08 580.708,1111.08 580.314,1110.69 C579.921,1110.29 579.921,1109.65 580.314,1109.26 L584.58,1104.99 L580.344,1100.76 C579.953,1100.37 579.953,1099.73 580.344,1099.34 C580.733,1098.95 581.367,1098.95 581.758,1099.34 L585.994,1103.58 L590.292,1099.28 C590.686,1098.89 591.323,1098.89 591.717,1099.28 C592.11,1099.68 592.11,1100.31 591.717,1100.71 L587.42,1105.01 L591.657,1109.24 L591.657,1109.24 Z M586,1089 C577.163,1089 570,1096.16 570,1105 C570,1113.84 577.163,1121 586,1121 C594.837,1121 602,1113.84 602,1105 C602,1096.16 594.837,1089 586,1089 L586,1089 Z" id="cross-circle" sketch:type="MSShapeGroup">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>`;

                $('#newinput').append(newRowAdd);
                count++;
            });

            $("body").on("click", ".remove-more", function() {
                $(this).parents(".row-div").remove();
            });
            $('.all-select2_dropdown').select2();

            $('#accountType').on('select2:select', function() {
                let value = $(this).val();
                if(value == 'credit') {
                    $('#walletAccountDiv').show();
                    $('#paymentGatewayDiv').show();
                } else {
                    $('#walletAccountDiv').hide();
                    $('#paymentGatewayDiv').hide();
                }
            })
        });
    </script>
</x-main>
