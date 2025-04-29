@extends('layouts.AuthLayout')

@section('title', 'Email Verification')

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
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}" id="resendForm">
                            @csrf
                            <button type="submit" id="resendButton" class="btn btn-link p-0 m-0 align-baseline">{{ __('Resend code') }}</button>
                        </form>
                    </p>

                    <p id="timer" class="text-danger"></p>
                </div>
                <div id="verificationMessage" class="alert" style="display: none;"></div>
                <form method="POST" action="{{ route('verification.verify') }}" id="verifyForm">
                    @csrf
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
                        <button type="submit" class="btn btn-primary" id="verifyButton">{{ __('Verify Email') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function moveToNext(current, nextFieldId) {
        if (current.value.length == current.maxLength) {
            if (nextFieldId) {
                document.getElementById(nextFieldId).focus();
            }
        }
    }

    $(document).ready(function() {
        let timeLeft = 30;
        let timerId;

        function startTimer() {
            clearInterval(timerId);
            timerId = setInterval(countdown, 1000);
            $('#resendButton').prop('disabled', true);
            resetOtpForm();
        }

        function countdown() {
            if (timeLeft == 0) {
                clearInterval(timerId);
                $('#timer').text('OTP has expired. You can now request a new one.');
                $('#resendButton').prop('disabled', false);
            } else {
                $('#timer').text(timeLeft + ' seconds remaining');
                timeLeft--;
            }
        }

        function resetOtpForm() {
            $('.otp-input').val('');
            $('#otp1').focus();
        }

        startTimer();

        $('.otp-input').on('keydown', function(e) {
            if (e.which === 8 && this.value.length === 0) {
                $(this).prev('.otp-input').focus();
            }
        });
        
        $('#verifyForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.redirect) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Verified',
                            text: 'You will be redirected to the dashboard.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = response.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: xhr.responseJSON.error
                    });
                    resetOtpForm(); // Reset form ketika OTP tidak sesuai
                }
            });
        });

        $('#resendForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Code Resent',
                        text: response.message
                    });
                    timeLeft = 30;
                    startTimer();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Resend Failed',
                        text: xhr.responseJSON.error || 'An error occurred while resending the code.'
                    });
                }
            });
        });
    });
</script>
@endsection