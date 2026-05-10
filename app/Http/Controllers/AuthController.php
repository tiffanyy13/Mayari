<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return $this->redirectByRole(Auth::user());
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user());
        }
        return back()->withErrors(['email' => 'Invalid credentials.'])
                     ->withInput($request->only('email'));
    }

    public function showRegister()
    {
        if (Auth::check()) return $this->redirectByRole(Auth::user());
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate(
            [
                'firstName' => 'required|string|max:100',
                'lastName'  => 'required|string|max:100',
                'phone'     => ['required', 'string', 'size:11', 'regex:/^09[0-9]{9}$/'],
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|min:8|confirmed',
            ],
            [
                'phone.required' => 'Please enter your mobile number.',
                'phone.size'     => 'Mobile number must be exactly 11 digits (e.g. 09171234567).',
                'phone.regex'    => 'Use a Philippine mobile number: 11 digits starting with 09 (e.g. 09171234567).',
            ]
        );
        $user = User::create([
            'firstName'   => $data['firstName'],
            'lastName'    => $data['lastName'],
            'phone' => $data['phone'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => 'customer',
        ]);
        Auth::login($user);
        $request->session()->put('show_address_welcome', true);
        return redirect()->route('customer.home')->with('success', 'Account created! Welcome to Mayari.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        return $user->isAdmin()
            ? redirect()->route('admin.orders')
            : redirect()->route('customer.home');
    }
}
