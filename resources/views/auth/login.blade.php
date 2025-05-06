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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('logout_success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('logout_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
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
                e.preventDefault(); // Always prevent default to handle submission manually

                let isValid = true;

                // Reset previous validation states
                emailInput.classList.remove('is-invalid');
                passwordInput.classList.remove('is-invalid');

                // Email validation
                const emailValue = emailInput.value.trim();
                if (!emailValue) {
                    emailInput.classList.add('is-invalid');
                    emailFeedback.textContent = 'Email is required';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                    emailInput.classList.add('is-invalid');
                    emailFeedback.textContent = 'Please enter a valid email address';
                    isValid = false;
                }

                // Password validation
                if (!passwordInput.value) {
                    passwordInput.classList.add('is-invalid');
                    passwordFeedback.textContent = 'Password is required';
                    isValid = false;
                }

                if (!isValid) {
                    return false;
                }

                // Show loading indicator
                loginButton.textContent = 'Logging in';
                loadingSpinner.classList.remove('d-none');

                // Submit form directly for reliable operation
                // This avoids JSON parsing issues when server returns unexpected formats
                loginForm.submit();
            });
        });
    </script>
@endsection
