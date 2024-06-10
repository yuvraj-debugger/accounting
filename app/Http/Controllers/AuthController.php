<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use DB;

use Redirect;

class AuthController extends Controller
{
    //
    public function index()
    {

        return view('auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);
        if(\Auth::attempt($request->only('email','password'))){
            return redirect('/');
        }else{

            return redirect('/login')->with('error', 'Invalid credentials');
        }

    }
    public function registerView()
    {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $request->validate([
           'name'=>'required',
            'email'=> 'required|unique:users|email',
            'password'=>'required|confirmed'

        ]);
        $user = User::create([
           'name'=>$request->name,
            'email'=>$request->email,
            'password'=> Hash::make($request->password),
        ]);
        if(\Auth::attempt($request->only('email','password'))){
            return redirect('/');
        }
        return redirect('register')->withErrors('error');
    }
    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('/login');
    }
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }
    public function resetPasswordSubmit(Request $request)
    {
        $request->validate([
             'email'=> 'required|exists:users|email',

         ]);
         $token = Str::random(64);

          DB::table('password_resets')->insert([
              'email' => $request->email,
              'token' => $token,
              'created_at' => date('Y-m-d H:i:s')
            ]);
            Mail::send('emails.forgetPassword', ['token' => $token], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset Password');
            });
            return back()->with('message', 'We have e-mailed your password reset link!');

    }
    public function showResetPasswordForm($token)
    {
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }
    public function submitResetPasswordForm(Request $request)
      {
          $request->validate([
              'email' => 'required|email|exists:users',
              'password' => 'required|string|confirmed',
              'password_confirmation' => 'required'
          ]);

          $updatePassword = DB::table('password_resets')
                              ->where([
                                'email' => $request->email,
                                'token' => $request->token
                              ])
                              ->first();

          if(!$updatePassword){
              return back()->withInput()->with('error', 'Invalid token!');
          }

          $user = User::where('email', $request->email)
                      ->update(['password' => Hash::make($request->password)]);

          DB::table('password_resets')->where(['email'=> $request->email])->delete();

          return redirect('login')->with('success', 'Your Password has been changed successfully');

      }
      public function userProfile()
      {
        return view('auth.user-profile');
      }
      public function profileUpdate(Request $request)
      {
          $request->validate([
              'name'=>'required',
              'email'=> 'required|email',
              
          ]);
        if(! empty($request->user_id)){
            $user = User::where('id', $request->user_id)->first();
            $user->email = $request->email ;
            $user->name =  $request->name ;
            $user->update();
            return redirect('/user-profile')->with('success', 'Profile updated successfully');
        }
      }
      public function updatePassword(Request $request)
      {
        $request->validate([
            'password'=>'required|confirmed'

         ]);
         if(! empty($request->user_id)){
            $user = User::where('id', $request->user_id)->first();
            $user->password = Hash::make($request->password);
            $user->update();
            return redirect('/user-profile')->with('message', 'Password updated successfully');
         }
      }


}
