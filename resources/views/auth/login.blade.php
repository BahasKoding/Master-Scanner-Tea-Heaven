@extends('layouts.AuthLayout')

@section('title', 'Login')

@section('content')
    <div class="auth-form">
        <div class="card my-5 shadow-sm">
            <div class="card-body p-4">
                <div class="text-center">
                    <img src="{{ asset('img/tea-heaven.png') }}" alt="Logo Tea Heaven" class="img-fluid mb-3"
                        style="max-height: 80px;">
                    <h4 class="fw-bold mb-4">Administrator Login</h4>
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" placeholder="Enter your email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>
                        </div>
                        <div id="emailFeedback" class="invalid-feedback"></div>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password" placeholder="Enter your password" required>
                            <button type="button" class="input-group-text bg-transparent" id="togglePassword">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div id="passwordFeedback" class="invalid-feedback"></div>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            <span id="loginButton">Login</span>
                            <span id="loadingSpinner" class="spinner-border spinner-border-sm ms-2 d-none"
                                role="status"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Client-side validation
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailFeedback = document.getElementById('emailFeedback');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const loginButton = document.getElementById('loginButton');
            const loadingSpinner = document.getElementById('loadingSpinner');

            loginForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Reset previous validation states
                emailInput.classList.remove('is-invalid');
                passwordInput.classList.remove('is-invalid');

                // Email validation
                const emailValue = emailInput.value.trim();
                if (!emailValue) {
                    e.preventDefault();
                    emailInput.classList.add('is-invalid');
                    emailFeedback.textContent = 'Email is required';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                    e.preventDefault();
                    emailInput.classList.add('is-invalid');
                    emailFeedback.textContent = 'Please enter a valid email address';
                    isValid = false;
                }

                // Password validation
                if (!passwordInput.value) {
                    e.preventDefault();
                    passwordInput.classList.add('is-invalid');
                    passwordFeedback.textContent = 'Password is required';
                    isValid = false;
                }

                if (isValid) {
                    // Show loading indicator
                    loginButton.textContent = 'Logging in';
                    loadingSpinner.classList.remove('d-none');
                }
            });

            // Show SweetAlert for session messages
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33'
                });
            @endif

            @if (session('logout_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Logout Successful',
                    text: "{{ session('logout_success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endsection
