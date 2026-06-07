<?php

namespace App\Http\Controllers\Auth;

use App\Enums\MemberCategory;
use App\Http\Controllers\Controller;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly MembershipService $membership)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'E-posta veya şifre hatalı.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->intended($user->isStaff() ? route('admin.dashboard') : route('uye.dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register', ['categories' => MemberCategory::cases()]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'category' => ['required', 'in:'.implode(',', array_column(MemberCategory::cases(), 'value'))],
            'password' => ['required', 'confirmed', Password::defaults()],
            'kvkk_consent' => ['accepted'],
            'commercial_consent' => ['nullable', 'boolean'],
        ]);

        $user = $this->membership->register($data);
        Auth::login($user);

        return redirect()->route('uye.dashboard')->with('status', 'Aramıza hoş geldin! Üyeliğin oluşturuldu.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seffaf-kasa');
    }
}
