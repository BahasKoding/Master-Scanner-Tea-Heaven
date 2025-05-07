<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        try {
            // Validate login data
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Check for too many login attempts
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                // Get time remaining before next attempt is allowed
                $seconds = $this->limiter()->availableIn(
                    $this->throttleKey($request)
                );

                $minutes = ceil($seconds / 60);

                $message = $minutes > 1
                    ? "Terlalu banyak percobaan login. Silakan coba lagi dalam $minutes menit."
                    : "Terlalu banyak percobaan login. Silakan coba lagi dalam $minutes menit.";

                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['email' => $message]);
            }

            // Get credentials
            $credentials = $this->credentials($request);

            // Check if user exists
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                $this->incrementLoginAttempts($request);

                // Record failed login attempt due to invalid email
                addActivity('auth', 'failed_login', 'Failed login attempt with email: ' . $credentials['email'], 0);

                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['email' => 'Email yang Anda masukkan tidak terdaftar.']);
            }

            // Attempt to log the user in
            if ($this->attemptLogin($request)) {
                // If login successful, handle successful login response
                return $this->sendLoginResponse($request);
            }

            // If login failed, increment the number of attempts
            $this->incrementLoginAttempts($request);

            // Record failed login attempt due to wrong password
            addActivity('auth', 'failed_login', 'Failed login attempt for user: ' . $user->name . ' (wrong password)', $user->id);

            // Wrong password
            return redirect()
                ->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['password' => 'Password yang Anda masukkan salah.']);
        } catch (Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'user_ip' => $request->ip()
            ]);

            return redirect()
                ->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors(['email' => 'Terjadi kesalahan saat login. Silakan coba lagi.']);
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        // Remember user if requested
        if ($request->filled('remember')) {
            Auth::setRememberDuration(43200); // 30 days
        }

        // Record successful login activity
        addActivity('auth', 'login', 'User logged in successfully', Auth::id());

        // Clear any intended URLs that might be invalid or causing issues
        if ($request->session()->has('url.intended')) {
            // Check if the intended URL contains favicon.png or other problematic paths
            $intendedUrl = $request->session()->get('url.intended');
            if (
                strpos($intendedUrl, 'favicon.png') !== false ||
                strpos($intendedUrl, '/images/') !== false ||
                strpos($intendedUrl, '.png') !== false ||
                strpos($intendedUrl, '.jpg') !== false ||
                strpos($intendedUrl, '.jpeg') !== false ||
                strpos($intendedUrl, '.gif') !== false ||
                strpos($intendedUrl, '.ico') !== false
            ) {
                // Clear the problematic intended URL
                $request->session()->forget('url.intended');
            }
        }

        // Explicitly redirect to dashboard to avoid problematic redirects
        return redirect('/dashboard')
            ->with('login_success', 'Login berhasil! Selamat datang di Dashboard Tea Heaven.');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Record logout activity before actually logging out
        if (Auth::check()) {
            addActivity('auth', 'logout', 'User logged out: ' . Auth::user()->name, Auth::id());
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('logout_success', 'Anda berhasil logout dari sistem.');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Clear login attempts
        $this->clearLoginAttempts($request);

        // Remember user if requested
        if ($request->filled('remember')) {
            Auth::setRememberDuration(43200); // 30 days
        }

        // Add success message to session
        session()->flash('login_success', 'Login berhasil! Selamat datang di Dashboard Tea Heaven.');

        // Check for intended url that might be problematic
        if ($request->session()->has('url.intended')) {
            $intendedUrl = $request->session()->get('url.intended');
            if (
                strpos($intendedUrl, 'favicon.png') !== false ||
                strpos($intendedUrl, '/images/') !== false ||
                strpos($intendedUrl, '.png') !== false ||
                strpos($intendedUrl, '.jpg') !== false ||
                strpos($intendedUrl, '.jpeg') !== false ||
                strpos($intendedUrl, '.gif') !== false ||
                strpos($intendedUrl, '.ico') !== false
            ) {
                // Clear the problematic intended URL
                $request->session()->forget('url.intended');
            }
        }

        // Always redirect to dashboard explicitly
        return redirect('/dashboard');
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        // Check if the dashboard route exists, otherwise use home
        if (Route::has('dashboard')) {
            return route('dashboard');
        }

        if (Route::has('home')) {
            return route('home');
        }

        // Fallback to root path if neither route exists
        return '/';
    }
}
