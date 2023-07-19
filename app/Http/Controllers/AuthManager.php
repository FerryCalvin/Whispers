<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthManager extends Controller
{
    function login(){
        if(Auth::check()){
            
        }
        return view('login');
    }

    function registration(){
        if(Auth::check()){
            return redirect(route('home'));
        }
        return view('registration');
    }

    //retrieve data from form
    function loginPost(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $minta = $request->only('email', 'password');
        if(Auth::attempt($minta)){
            return redirect()->intended(route('home'))->with("success", "Access Granted!");
        }
        return redirect(route('login'))->with("error", "Login Invalid!");
    }

    function registrationPost(Request $request){
        $request->validate([
            'name' => ['required', 'min:1', 'max:18', Rule::unique('users', 'name')],
            'email' => ['required', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'max:18']
        ]);

        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        if(!$user){
            return redirect(route('registration'))->with("error", "Registration Failed, try again!");
        }
        return redirect(route('login'))->with("success", "Registration Success!");

    }

    function logout(){
        Session::flush();
        Auth::logout();
        return redirect(route('login'));

    }

}
