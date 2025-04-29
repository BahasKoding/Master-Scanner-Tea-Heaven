@extends('layouts.AuthLayout')

@section('title', 'Login')

@section('content')
    <div class="auth-form">
        <div class="card my-5">
            <div class="card-body">
                <div id="loginErrorMessage" class="alert alert-danger" style="display: none;"></div>
                <div class="text-center">
                    <img src="{{ asset('img/tea-heaven.png') }}" alt="Logo Tea Heaven" class="img-fluid mb-2">
                    <h4 class="f-w-500 mb-3 mt-3">LOGIN HERE</h4>
                    <!--<p class="mb-3">Don't have an Account? <a href="{{ route('register') }}"
                                    class="link-primary ms-1">Create Account</a></p>-->
                </div>
                <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" name="email" required autocomplete="name"
                            value="admin@gmail.com" autofocus id="email" placeholder="Your Email">
                        <span id="emailError" class="text-danger"></span>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" name="password" required autocomplete="current-password"
                            value="12345678" id="password" placeholder="Password">
                        <span id="passwordError" class="text-danger"></span>
                    </div>
                    <div class="d-flex mt-1 justify-content-between align-items-center">
                        <div class="form-check">
                            {{-- <input class="form-check-input input-primary" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="remember">Remember me?</label> --}}
                        </div>
                        <a href="#">
                            {{-- <h6 class="f-w-400 mb-0">Forgot Password?</h6> --}}
                        </a>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            @if (session('logout_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Logout Successful',
                    text: '{{ session('
                                                    logout_success ') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Successful',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                html: errorMessage,
                                confirmButtonText: 'Try Again'
                            });
                        } else if (xhr.status === 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'User Not Found',
                                text: 'The email you entered is not registered. Would you like to create an account?',
                                showCancelButton: true,
                                confirmButtonText: 'Register',
                                cancelButtonText: 'Try Again'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('register') }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: 'An unexpected error occurred. Please try again later.',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
