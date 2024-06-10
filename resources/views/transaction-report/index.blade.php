<?php
    use App\Http\Controllers\TransactionReportController;
?>
<x-main>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container" style="max-width: 100%">
            <div class="page-title">
                <h3>Income Transaction Report</h3>
            </div>
            <div class="row">

                <div class="col-md-12 col-lg-12">
                    {{-- <a href="{{route('transaction-rule.create')}}" class="btn btn-primary mb-2"> <span class="fa-fw select-all fas"></span> Create</a> --}}
                    <div class="row mb-2">
                        <form method="get">
                            <div class="col-md-12 col-lg-12">
                                <select name="financial_year" class="form-control mb-2" style="width: 15%;">
                                    <option value="">Select Financial Year</option>
                                    @foreach ($financialYearsData as $key => $financialYear)
                                        <option value="{{ $key }}"
                                            {{ $selectedFinancialYear == $key ? 'selected' : '' }}>{{ $financialYear }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary mb-2">Search</button>
                                <a href="/transaction-report" class="btn btn-primary mb-2">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="card">
                        {{-- <div class="card-header">Transaction Rule</div> --}}
                        <div class="card-body">
                            @php
                                $html = '';
                            @endphp

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            @foreach ($calaculatedFiscalYearForDate as $year)
                                                <th class="">{{ date('M Y', strtotime($year)) }}</th>
                                                @php
                                                    $html .= '<th class=""></th>';
                                                @endphp
                                            @endforeach
                                            <th>Total</th>
                                            <th>Average</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accountTypes as $accountType)
                                            @php
                                                $accountType_format = ucfirst(str_replace('-', ' ', $accountType));
                                                $account_data = [];
                                            @endphp
                                            <tr class="parentHeading">
                                                <th class="" colspan="2">{{ $accountType_format }}</th>
                                                @foreach ($calaculatedFiscalYearForDate as $year)
                                                    <th class=""></th>
                                                @endforeach
                                                <th class=""></th>
                                                {{-- <th class=""></th> --}}
                                            </tr>
                                            @foreach ($accountModel->tree($accountType) as $account)
                                                <tr>
                                                    <td class=""><strong>{{ $account->name }}</strong></td>
                                                    @foreach ($calaculatedFiscalYearForDate as $year)
                                                        <td class=""></td>
                                                    @endforeach
                                                    <td class=""></td>
                                                    <td class=""></td>
                                                </tr>
                                                @foreach ($account->childern as $account_childern)
                                                    <tr>
                                                        <td class=" text-sm font-medium">
                                                            {{ $account_childern->name }}</td>
                                                        @php
                                                            $total = 0;
                                                            $count = 0;
                                                            $account_data[] = $account_childern->id;
                                                        @endphp
                                                        @foreach ($calaculatedFiscalYearForDate as $year)
                                                            @php
                                                                $income = TransactionReportController::incomeExpenseReport(
                                                                    $account_childern->id,
                                                                    date('m', strtotime($year)),
                                                                    date('Y', strtotime($year)),
                                                                );
                                                                $total = $total + $income;
                                                                ++$count;
                                                            @endphp
                                                            <td class="">&#8377;{{ number_format($income) }}
                                                            </td>
                                                        @endforeach
                                                        <td class="">
                                                            <strong>&#8377;{{ number_format($total) }}</strong></td>
                                                        <td class=" text-sm font-medium">
                                                            <strong>&#8377;{{ number_format(!empty($count) ? round($total / $count, 2) : 0) }}</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            @php
                                                $total_amount = 0;
                                                $count_amount = 0;
                                            @endphp
                                            <tr style="background-color: #cfedc3">
                                                <th class="">Total {{ $accountType_format }}</th>
                                                @foreach ($calaculatedFiscalYearForDate as $year)
                                                    @php
                                                        if ($accountType == 'income') {
                                                            $amount_data = TransactionReportController::incomeExpenseMonthTotal(
                                                                date('m', strtotime($year)),
                                                                date('Y', strtotime($year)),
                                                            );
                                                            $total_amount = $total_amount + $amount_data;
                                                            ++$count_amount;
                                                        } elseif ($accountType == 'expense') {
                                                            $amount_data = TransactionReportController::expenseMonthTotal(
                                                                date('m', strtotime($year)),
                                                                date('Y', strtotime($year)),
                                                            );
                                                            $total_amount = $total_amount + $amount_data;
                                                            ++$count_amount;
                                                        } else {
                                                            $amount_data = TransactionReportController::profitExpenseMonthTotal(
                                                                date('m', strtotime($year)),
                                                                date('Y', strtotime($year)),
                                                            );
                                                            $total_amount = $total_amount + $amount_data;
                                                            ++$count_amount;
                                                        }
                                                    @endphp
                                                    <td class="">&#8377;{{ number_format($amount_data) }}</td>
                                                @endforeach
                                                <td class="">&#8377;{{ number_format($total_amount) }}</td>
                                                <td class="">
                                                    &#8377;{{ number_format(!empty($count_amount) ? round($total_amount / $count_amount, 2) : 0) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                @foreach ($calaculatedFiscalYearForDate as $year)
                                                <td class="uppercase"></td>
                                                @endforeach
                                                <td class="uppercase"></td>
                                                <td class="uppercase"></td>
                                                <td class="uppercase"></td>
                                            </tr>
                                        @endforeach

                                        {{-- Savings Report start --}}
                                        <tr class="parentHeading">
                                            <th class="" colspan="2">Saving</th>
                                            @foreach($calaculatedFiscalYearForDate as $year)
                                                <th class=""></th>
                                            @endforeach
                                            <th class=""></th>
                                        </tr>
                                        @foreach($saving as $bankaccounts)
                                            <tr>
                                                <td class="text-sm font-medium">{{$bankaccounts->name}}</td>
                                                @php
                                                    $total=0;
                                                    $count=0;
                                                    $account_data[] = $bankaccounts->id;
                                                @endphp
                                                @foreach($calaculatedFiscalYearForDate as $year)
                                                    @php
                                                        $incomeExpense = TransactionReportController::savingsReport($bankaccounts->id, date('m', strtotime($year)), date('Y', strtotime($year)));
                                                        $total=$total+$incomeExpense;
                                                        ++$count;
                                                    @endphp
                                                <td class="">&#8377;{{number_format($incomeExpense)}}</td>
                                                @endforeach
                                                <td class=""><strong>&#8377;{{number_format($total)}}</strong></td>
                                                <td class="text-sm font-medium"><strong>&#8377;{{number_format(!empty($count)?round(($total/$count),2):0)}}</strong></td>
                                            </tr>
                                        @endforeach
                                        @php
                                                $total_amount = 0;
                                                $count_amount = 0;
                                            @endphp
                                            <tr style="background-color: #cfedc3">
                                                <th class="">Total Saving</th>
                                                @foreach ($calaculatedFiscalYearForDate as $year)
                                                    @php
                                                        $amount_data = TransactionReportController::savingMonthTotal(
                                                            date('m', strtotime($year)),
                                                            date('Y', strtotime($year)),
                                                        );
                                                        $total_amount = $total_amount + $amount_data;
                                                        ++$count_amount;
                                                    @endphp
                                                    <td class="">&#8377;{{ number_format($amount_data) }}</td>
                                                @endforeach
                                                <td class="">&#8377;{{ number_format($total_amount) }}</td>
                                                <td class="">
                                                    &#8377;{{ number_format(!empty($count_amount) ? round($total_amount / $count_amount, 2) : 0) }}
                                                </td>
                                            </tr>
                                        {{-- Savings Report end --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main>
