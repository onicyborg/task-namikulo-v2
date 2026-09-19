<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function auth(Request $req)
    {
        $credentials = $req->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $remember = false;
        if (request()->has('remember') && request('remember') === 'on') {
            $remember = true;
        }

        $username = htmlspecialchars($credentials['username'], ENT_QUOTES, 'UTF-8');
        $password = $credentials['password'];

        $user = User::where('username', $username)->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $req->session()->regenerate();
            $response['status'] = '1';
            $response['msg'] = 'Loggin berhasil';
            $response['url'] = url('dashboard');
        } else {
            $response['status'] = '0';
            $response['msg'] = 'Periksa username dan password';
        }

        return response()->json($response);
    }
    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect()->route('login');
    }
}
