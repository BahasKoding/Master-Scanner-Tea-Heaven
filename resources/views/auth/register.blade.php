@extends('layouts.AuthLayout')

@section('title', 'Register')

@section('content')
    <div class="auth-form">
        <div class="card my-5">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="text-center">
                    <img src="{{ URL::asset('build/images/authentication/img-auth-register.png') }}" alt="images" class="img-fluid mb-3">
                    <h4 class="f-w-500 mb-1">Register with your email</h4>
                    <p class="mb-3">Already have an Account? <a href="{{ route('login.submit') }}" class="link-primary">Log in</a></p>
                </div>
                <form id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter name">
                    </div>
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email Address">
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" name="password" required autocomplete="new-password" placeholder="Password">
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = '{{ session('redirect') }}';
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            text: '{{ session('error') }}'
        });
    @endif
</script>
@endsection