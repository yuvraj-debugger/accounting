<nav id="sidebar" class="active">
    <div class="sidebar-header">
        Accounting Software
    </div>
    <ul class="list-unstyled components text-secondary">
        <li>
            <a href="/"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
            <a href="{{route('account.index')}}"><i class="fas fa-file-alt"></i> Accounts</a>
        </li>
        <li>
            <a href="{{route('bank.index')}}"><i class="fas fa-table"></i> Banks</a>
        </li>
        <li>
            <a href="{{route('transaction.index')}}"><i class="fas fa-chart-bar"></i> Transactions</a>
        </li>
        <li>
            <a href="{{route('transaction-rule.index')}}"><i class="fas fa-chart-bar"></i> Transaction Rules</a>
        </li>

        <li>
            <a href="{{route('transaction-report.index')}}"><i class="fas fa-chart-bar"></i> Income Transaction Report</a>
        </li>

        <li>
            <a href="{{route('transaction-summary.index')}}"><i class="fas fa-chart-bar"></i> Income Transaction Summary</a>
        </li>

    </ul>
</nav>
<style>
    .sidebar-header{
        color:green;
        font-size:23px;
    }
</style>
