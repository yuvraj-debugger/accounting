<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;

class AccountController extends Controller
{

    public function index()
    {
        $search = $_GET['search_account'] ?? '';
        $accountTypeSearch = $_GET['type'] ?? '';

        $accounts = Account::where('status', Account::STATE_ACTIVE)->orderBy('id', 'DESC');
        if (! empty($search)) {
            $accounts = $accounts->where('name', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%');
        }

        if(!empty($accountTypeSearch)) {
            $accounts = $accounts->where('account_type', $accountTypeSearch);
        }

        $accounts = $accounts->paginate(10);
        $accountTypes = Account::typeOptions();

        return view('account.index', compact('accounts', 'search', 'accountTypes'));
    }

    public function create()
    {
        $accountTypes = Account::typeOptions();
        $accounts = Account::where('status', Account::STATE_ACTIVE)->where(function ($query) {
            $query->whereIn('parent_account_name', [
                '',
                0,
                null
            ])
                ->orWhereNull('parent_account_name');
        })
            ->get();
        return view('account.create', compact('accountTypes', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
            'account_type' => 'required',
        ]);
        $account = self::addAccount($request);
        return redirect('/account')->with('success', 'Account added successfully');
    }

    public function addAcccountFromTransactions(Request $request) {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
            'account_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $account = self::addAccount($request);
        if($account) {
            $jsonResponse = [
                'success' => true,
                'message' => 'Account added successfully.'
            ];
        } else {
            $jsonResponse = [
                'success' => false,
                'message' => 'Account not added.'
            ];
        }
        return response()->json($jsonResponse);
    }

    public static function addAccount($request) {
        $account = Account::create([
            'name' => $request->account_name,
            'account_type' => $request->account_type,
            'description' => $request->description,
            'parent_account_name' => $request->parent_account_name,
            'opening_balance' =>!empty($request->opening_balance)? $request->opening_balance : 0,
            'status' => Account::STATE_ACTIVE,
        ]);
        return $account;
    }

    public function update($id)
    {
        $accountData = Account::find($id);

        $accountTypes = Account::typeOptions();
        $accounts = Account::where('status', Account::STATE_ACTIVE)->where(function ($query) {
            $query->whereNull('parent_account_name');
        })
            ->where('id', '!=', $id)
            ->get();

        return view('account.update', compact('accountData', 'accountTypes', 'accounts'));
    }

    public function storeupdate(Request $request, $id)
    {
        $request->validate([
            'account_name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
            'account_type' => 'required',
//             'opening_balance' => 'numeric'
        ]);
        $account = Account::find($id);
        $account->name = $request->account_name;
        $account->description = $request->description;
        $account->account_type = $request->account_type;
        $account->parent_account_name = $request->parent_account_name;
        $account->opening_balance = $request->opening_balance;
        $account->save();
        return redirect('/account')->with('success', 'Account updated successfully');
    }

    public function delete($id)
    {
        $account = Account::find($id);
        $account->status = Account::STATE_DELETED;
        $account->update();
        return redirect()->route('account.index');
    }

    public function accountDeleteAll(Request $request)
    {
        if (! empty($request->all_ids)) {
            $accounts = Account::whereIn('id', explode(',', $request->all_ids))->get();
            foreach ($accounts as $account) {
                $account->status = 2;
                $account->update();
            }
            Session::flash('info', 'Account deleted successfully');
        }
    }
}
