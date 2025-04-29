@extends('layouts.AuthLayout')

@section('title', 'Verify OTP')

@section('content')
<div class="auth-form">
    <div class="card my-5">
        <div class="card-body">
            <div class="text-center">
                <img src="{{ URL::asset('build/images/authentication/img-auth-code-varify.png') }}" alt="images" class="img-fluid mb-3">
                <h4 class="f-w-500 mb-1">{{ __('Verify Your Email Address') }}</h4>
                <p class="mb-0">{{ __('We\'ve sent you a verification code on your email.') }}</p>
                <p class="mb-3" id="resendWrapper" style="display: none;">
                    {{ __('Did not receive the email?') }} 
                    <button type="button" id="resendButton" class="btn btn-link p-0 m-0 align-baseline">{{ __('Resend code') }}</button>
                </p>
                <p id="timer" class="text-danger"></p>
            </div>
            <div id="verificationMessage" class="alert" style="display: none;"></div>
            <form id="verifyForm">
                @csrf
                <input type="hidden" name="email" id="email" value="{{ $email }}">
                <div class="row my-4 g-3 text-center">
                    <div class="col">
                        <input type="text" class="form-control text-center otp-input" id="otp1" name="digit1" maxlength="1" required autofocus oninput="moveToNext(this, 'otp2')">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control text-center otp-input" id="otp2" name="digit2" maxlength="1" required oninput="moveToNext(this, 'otp3')">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control text-center otp-input" id="otp3" name="digit3" maxlength="1" required oninput="moveToNext(this, 'otp4')">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control text-center otp-input" id="otp4" name="digit4" maxlength="1" required oninput="moveToNext(this, '')">
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary" id="verifyButton">{{ __('Verify OTP') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function moveToNext(current, nextFieldId) {
    if (current.value.length == current.maxLength) {
        if (nextFieldId) {
            document.getElementById(nextFieldId).focus();
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = 30;
    let timerId;

    function startTimer() {
        clearInterval(timerId);
        timerId = setInterval(countdown, 1000);
        document.getElementById('resendButton').disabled = true;
        document.getElementById('resendWrapper').style.display = 'none';
        resetOtpForm();
    }

    function countdown() {
        if (timeLeft == 0) {
            clearInterval(timerId);
            document.getElementById('timer').textContent = 'OTP has expired. You can now request a new one.';
            document.getElementById('resendButton').disabled = false;
            document.getElementById('resendWrapper').style.display = 'block';
        } else {
            document.getElementById('timer').textContent = timeLeft + ' seconds remaining';
            timeLeft--;
        }
    }

    function resetOtpForm() {
        document.querySelectorAll('.otp-input').forEach(input => input.value = '');
        document.getElementById('otp1').focus();
    }

    startTimer();

    document.getElementById('verifyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const otp = document.getElementById('otp1').value +
                    document.getElementById('otp2').value +
                    document.getElementById('otp3').value +
                    document.getElementById('otp4').value;
        const email = document.getElementById('email').value;

        fetch('{{ route("password.verify.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                otp: otp
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                }).then(function() {
                    window.location.href = data.redirect;
                });
            } else if (data.error) {
                throw new Error(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An error occurred. Please try again.'
            });
            resetOtpForm();
        });
    });

    document.getElementById('resendButton').addEventListener('click', function() {
        const email = document.getElementById('email').value;
        fetch('{{ route("password.email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                });
                timeLeft = 30;
                startTimer();
            } else {
                throw new Error(data.error || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An error occurred. Please try again.'
            });
        });
    });

    // Email sudah diset dari server, jadi tidak perlu mengambil dari URL
    console.log('Email set:', document.getElementById('email').value);
});
</script>
@endsection