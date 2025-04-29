<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Tambahkan baris ini

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show(Request $request)
    {
        $userId = session('user_id') ?? $request->user()->id ?? null;
        if (!$userId) {
            return redirect()->route('login');
        }
        $user = User::findOrFail($userId);
        $maskedEmail = $this->maskEmail($user->email);
        
        return view('auth.verify', ['email' => $maskedEmail]);
    }

    public function verify(Request $request)
    {
        try {
            $userId = session('user_id') ?? $request->user()?->id ?? null;
            if (!$userId) {
                throw new \Exception('User not found. Please try logging in again.');
            }
            $user = User::findOrFail($userId);
    
            $otp = $request->digit1 . $request->digit2 . $request->digit3 . $request->digit4;
    
            if ($user->otp != $otp) {
                throw new \Exception('The OTP is incorrect.');
            }
    
            if ($user->hasVerifiedEmail()) {
                return redirect($this->redirectPath());
            }
    
            if ($user->markEmailAsVerified()) {
                $user->otp = null;
                $user->save();
            }
    
            Auth::login($user); // Tambahkan baris ini untuk login user setelah verifikasi
    
            return redirect($this->redirectPath())->with('verified', true);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function resend(Request $request)
    {
        $userId = session('user_id') ?? $request->user()?->id ?? null;
        if (!$userId) {
            return back()->with('error', 'Unable to find user. Please try logging in again.');
        }
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $user->otp = sprintf('%04d', mt_rand(0, 9999));
        $user->save();

        $user->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }


    protected function maskEmail($email)
    {
        $atPosition = strpos($email, '@');
        return substr($email, 0, 2) . str_repeat('*', $atPosition - 2) . substr($email, $atPosition);
    }
}