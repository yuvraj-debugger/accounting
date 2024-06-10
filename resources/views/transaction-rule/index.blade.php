<x-main>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container" style="max-width: 100%">
            <div class="page-title">
                <h3>Transaction Rule</h3>
            </div>
            <div class="row">

                @if (Session::has('success'))
                    <div class="alert alert-success" id="success" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if (Session::has('danger'))
                    <div class="alert alert-danger" id="danger" role="alert">
                        {{ Session::get('danger') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div style="display: flex; justify-content: space-between;">
                            <a href="{{ route('transaction-rule.create') }}" class="btn btn-primary" style="padding-top: 7px; padding-left: 8px; height: 40px;">
                                <span class="fa-fw select-all fas"></span>
                                Create
                            </a>

                            <div class="col-md-3">
                                <form method="get">
                                    <div class="col-md-12 col-lg-12 d-flex justify-content-between">
                                        <input type="search" id="search" name="search_rule"
                                            value="{{ $search }}" class="form-control mb-2"
                                            placeholder="Search transaction..." style="width: 67%" />
                                        <button type="submit" class="btn btn-primary mb-2">Search</button>
                                        <a href="/transaction-rule" class="btn btn-primary mb-2">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">Transaction Rule</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Rule</th>
                                                <th>Account Type</th>
                                                <th>Account</th>
                                                <th>Category</th>
                                                <th>Criteria</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactionRules as $transactionRule)
                                                <tr>
                                                    <td scope="row">{{ $transactionRule->rule_name }}</td>
                                                    <td>{{ $transactionRule->account_type }}</td>
                                                    <td>{{ $transactionRule->getAccountName() }}</td>
                                                    <td>{{ $transactionRule->transactions_category_type }}</td>
                                                    <td>
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <th>Description</th>
                                                                <th>Condition</th>
                                                                <th>Value</th>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($transactionRule->criteria['value'] as $index => $value)
                                                                    <tr>
                                                                        <td>{{ $transactionRule->criteria['description'][$index] }}
                                                                        </td>
                                                                        <td>{{ $transactionRule->criteria['condition'][$index] }}
                                                                        </td>
                                                                        <td>{{ $value }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('transaction-rule.update', $transactionRule->id) }}" class="btn btn-success">
                                                            <span class="fa-fw select-all fas"></span>
                                                        </a>
                                                        <a href="{{ route('transaction-rule.delete', $transactionRule->id) }}" class="btn btn-danger">
                                                            <span class="fa-fw select-all fas"></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $transactionRules->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            $('#success').hide();
        }, 3000);
        setTimeout(function() {
            $('#danger').hide();
        }, 3000);
    </script>
</x-main>
