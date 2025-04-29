@extends('layouts.AuthLayout')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-form">
    <div class="card my-5">
        <div class="card-body">
            <div id="forgotPasswordErrorMessage" class="alert alert-danger" style="display: none;"></div>
            <div class="text-center">
                <img src="{{ URL::asset('build/images/authentication/img-auth-fporgot-password.png') }}" alt="images" class="img-fluid mb-3">
                <h4 class="f-w-500 mb-1">Forgot Password</h4>
                <p class="mb-3">Remember your password? <a href="{{ route('login') }}" class="link-primary ms-1">Back to Login</a></p>
            </div>
            <form id="forgotPasswordForm">
                @csrf
                <div class="form-group mb-3">
                    <input type="email" class="form-control" name="email" required autocomplete="email" autofocus id="email" placeholder="Email Address">
                    <span id="emailError" class="text-danger"></span>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Send OTP</button>
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
    $('#forgotPasswordForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("password.email") }}',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + ' (' + key + ')<br>';
                    });
                    $('#forgotPasswordErrorMessage').html(errorMessage).show();
                } else if (xhr.status === 404) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Not Found',
                        text: 'The email address you entered is not registered. Please check your email or register for a new account.',
                        footer: '<a href="{{ route("register") }}">Register a new account</a>'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again later.'
                    });
                }
            }
        });
    });
});
</script>
@endsection