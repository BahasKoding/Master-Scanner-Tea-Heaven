@extends('layouts.AuthLayout')

@section('title', 'Reset Password')

@section('content')
<div class="auth-form">
    <div class="card my-5">
        <div class="card-body">
            <div class="text-center">
                <img src="{{ URL::asset('build/images/authentication/img-auth-reset-password.png') }}" alt="images" class="img-fluid mb-3">
                <h4 class="f-w-500 mb-1">Reset password</h4>
                <p class="mb-3">Back to <a href="{{ route('login') }}" class="link-primary ms-1">Log in</a></p>
            </div>
            <form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(json => Promise.reject(json));
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message
            }).then(function() {
                window.location.href = data.redirect;
            });
        })
        .catch(error => {
            let errorMessage = 'An error occurred. Please try again.';
            if (error.errors) {
                errorMessage = Object.values(error.errors).flat().join('\n');
            } else if (error.message) {
                errorMessage = error.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        });
    });
});
</script>
@endsection