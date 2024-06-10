<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BankController extends Controller
{
    //
    public function index()
    {
        $search = $_GET['search_bank'] ?? '';
        if(! empty($search)){
            $banks = BankAccount::where('name','like','%'.$search.'%')->orWhere('description','like','%'.$search.'%');
        }else{
            $banks=BankAccount::where('status',BankAccount::STATE_ACTIVE)->orderBy('id','DESC');
            
        }
        $banks = $banks->paginate(10);
        return view('bank.index',compact('banks','search'));
    }
    public function create()
    {
        $currencies=Currency::get();
        $bankAccountTypes=BankAccount::accountTypeOption();
        return view('bank.create',compact('currencies','bankAccountTypes'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
            'bank_account_type' => 'required',
            'opening_balance'=>'numeric'
        ]);
        $bank = new BankAccount();
        $bank->name=$request->name;
        $bank->description=$request->description;
        $bank->bank_account_type=$request->bank_account_type;
        $bank->opening_balance=($request->opening_balance)?$request->opening_balance:'0';
        $bank->currency=$request->currency;
        $bank->status=BankAccount::STATE_ACTIVE;
        $bank->created_by=Auth::user()->id;
        $bank->running_balance=0;
        $bank->save();
        return redirect('/bank')->with('success', 'Bank added successfully');
    }
    public function update($id)
    {
        $currencies=Currency::get();
        $bankAccountTypes=BankAccount::accountTypeOption();
        $bank = BankAccount::find($id);
        return view('bank.update', compact('bank','currencies','bankAccountTypes'));
    }
    public function storeupdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
            'bank_account_type' => 'required',
            'opening_balance'=>'numeric'
        ]);
        $bank = BankAccount::find($id);
        $bank->name=$request->name;
        $bank->description=$request->description;
        $bank->bank_account_type=$request->bank_account_type;
        $bank->opening_balance=($request->opening_balance)?$request->opening_balance:'0';
        $bank->currency=$request->currency;
        $bank->status=BankAccount::STATE_ACTIVE;
        $bank->created_by=Auth::user()->id;
        $bank->save();
        return redirect('/bank')->with('success', 'Bank updated successfully');
    }
    public function delete($id)
    {
        $bank = BankAccount::find($id);
        $bank->status=BankAccount::STATE_DELETE;
        $bank->update();
        return redirect('/bank')->with('info', 'Bank updated successfully');
    }
    public function bankDeleteAll(Request $request) {
        if(! empty($request->all_ids)){
            $banks= BankAccount::whereIn('id',explode(',',$request->all_ids))->get();
            foreach ($banks as $bank){
                $bank->status = 2;
                $bank->update();
            }
            Session::flash('info', 'Bank deleted successfully');
        }
    }
}
