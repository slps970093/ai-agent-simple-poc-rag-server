<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AdminStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'account' => '帳號或密碼錯誤',
            ]);
        }

        $admin = Auth::guard('admin')->user();

        if ($admin->status !== AdminStatus::Active) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'account' => '此帳號已被停用',
            ]);
        }

        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
