<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOTP;
use App\Models\Backend\Activity;

class AuthController extends Controller
{
    use RegistersUsers {
        RegistersUsers::guard as registerGuard;
    }
    use AuthenticatesUsers {
        AuthenticatesUsers::guard as authenticateGuard;
    }
    use VerifiesEmails;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'verify', 'resend']);
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    // Gunakan method guard() dari AuthenticatesUsers
    protected function guard()
    {
        return $this->authenticateGuard();
    }


    // Login methods
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Tambahkan aktivitas logout
        addActivity('auth', 'logout', 'User logged out', $userId);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Logged out successfully', 'redirect' => url('/')]);
        }

        return redirect('/')->with('logout_success', 'You have been successfully logged out.');
    }

    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (!$user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Please verify your email.', 'redirect' => route('verification.notice')]);
            }

            if ($this->attemptLogin($request)) {
                // Tambahkan aktivitas login
                addActivity('auth', 'login', 'User logged in', $user->id);
                return response()->json(['message' => 'Login successful', 'redirect' => $this->redirectPath()]);
            }

            return response()->json(['errors' => ['email' => ['These credentials do not match our records.']]], 422);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    // Registration methods
    public function showRegistrationForm()
    {
        return redirect()->route('login');
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Set OTP dan waktu kedaluwarsa
        $user->otp = sprintf('%04d', mt_rand(0, 9999));
        $user->otp_expires_at = Carbon::now()->addMinutes(5); // Berikan waktu 5 menit
        $user->save();

        event(new Registered($user));

        $user->sendEmailVerificationNotification();

        session(['user_id' => $user->id]);

        // Tambahkan aktivitas registrasi
        addActivity('auth', 'register', 'New user registered', $user->id);

        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful. Please verify your email.')
            ->with('redirect', route('verification.notice'));
    }
    // Verification methods

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

    public function resend(Request $request)
    {
        $userId = session('user_id') ?? $request->user()?->id ?? null;
        if (!$userId) {
            return new JsonResponse(['error' => 'Unable to find user. Please try logging in again.'], 422);
        }
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            return new JsonResponse(['message' => 'Email already verified', 'redirect' => $this->redirectPath()]);
        }

        $user->otp = sprintf('%04d', mt_rand(0, 9999));
        $user->otp_expires_at = Carbon::now()->addSeconds(30);
        $user->save();

        $user->sendEmailVerificationNotification();

        return new JsonResponse(['message' => 'A new verification code has been sent to your email address.']);
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

            if (Carbon::now()->isAfter($user->otp_expires_at)) {
                throw new \Exception('The OTP has expired. Please request a new one.');
            }

            if ($user->hasVerifiedEmail()) {
                return new JsonResponse(['message' => 'Email already verified', 'redirect' => $this->redirectPath()]);
            }

            if ($user->markEmailAsVerified()) {
                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();
                // Tambahkan aktivitas verifikasi email
                addActivity('auth', 'verify_email', 'User verified email', $user->id);
            }

            Auth::login($user);

            return new JsonResponse(['message' => 'Email verified successfully', 'redirect' => $this->redirectPath()]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 422);
        }
    }

    protected function maskEmail($email)
    {
        $atPosition = strpos($email, '@');
        return substr($email, 0, 2) . str_repeat('*', $atPosition - 2) . substr($email, $atPosition);
    }

    public function forgotPass()
    {
        return view('auth.passwords.forgot');
    }

    public function forgotPassword(Request $request)
    {

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $otp = sprintf('%04d', mt_rand(0, 9999));
            $user->otp = $otp;
            $user->otp_expires_at = now()->addSeconds(30); // OTP berlaku 30 detik
            $user->save();

            Mail::to($user->email)->send(new PasswordResetOTP($user, $otp));


            return response()->json([
                'message' => 'OTP sent to your email.',
                'redirect' => route('password.verify', ['email' => $user->email])
            ]);
        }

        return response()->json(['error' => 'User not found'], 404);
    }

    public function showVerifyPasswordForm(Request $request)
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if (!$user || !$user->otp_expires_at) {
            return redirect()->route('forgot-password')->with('error', 'Invalid or expired request. Please try again.');
        }

        $expiresAt = $user->otp_expires_at;

        return view('auth.passwords.verifypass', [
            'email' => $email,
            'expiresAt' => $expiresAt
        ]);
    }

    public function verifyPasswordOTP(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|digits:4',
            ]);

            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('otp_expires_at', '>', now())
                ->first();

            if ($user) {
                $token = Str::random(60);
                $user->forceFill([
                    'password_reset_token' => $token,
                ])->save();


                return response()->json([
                    'message' => 'OTP verified successfully.',
                    'redirect' => route('password.reset', $token)
                ]);
            }

            return response()->json(['error' => 'Invalid OTP'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            $user = User::where('email', $request->email)
                ->where('password_reset_token', $request->token)
                ->first();

            if (!$user) {
                return response()->json(['error' => 'Invalid token'], 400);
            }

            $user->password = Hash::make($request->password);
            $user->password_reset_token = null;
            $user->save();

            // Tambahkan aktivitas reset password
            addActivity('auth', 'reset_password', 'User reset password', $user->id);


            return response()->json([
                'message' => 'Password has been reset successfully.',
                'redirect' => '/'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while resetting the password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
