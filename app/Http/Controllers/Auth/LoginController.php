<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->hasVerifiedEmail()) {
            auth()->logout();
            return redirect()->route('verification.notice');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);

            // Attempt to find the user
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['These credentials do not match our records.'],
                ]);
            }

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        } catch (Exception $e) {
            Log::error('Login error', ['message' => $e->getMessage(), 'email' => $request->email]);
            return back()->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['login_error' => $e->getMessage()]);
        }
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        Log::info('Attempting login', ['email' => $credentials['email']]);
        $result = $this->guard()->attempt(
            $credentials,
            $request->filled('remember')
        );
        Log::info('Login attempt result', ['success' => $result]);
        return $result;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
