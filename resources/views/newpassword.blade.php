@extends('guest-layout')

@section('content-header1')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card card-container">
                    <img class="card-img-top" src="{{ asset('img/ps.png') }}" style="width:auto;height:200px;">
                    <div class="centered">Set New Password</div>
                </div>
                <div class="card-body">
                    <!-- Display Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Password Reset Form -->
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email Field -->
                        <div class="form-floating mb-3">
                            <label for="email" class="entries">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password Field with Eye Icon -->
                        <div class="form-floating mb-3">
                            <label for="passwordReset" class="entries">New Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password') is-invalid @enderror" id="passwordReset" type="password" name="password" required placeholder="New password">
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="togglePasswordReset"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password Field with Eye Icon -->
                        <div class="form-floating mb-3">
                            <label for="passwordConfirmationReset" class="entries">Confirm Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror" id="passwordConfirmationReset" type="password" name="password_confirmation" required placeholder="Confirm password">
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="fa fa-eye" id="toggleConfirmPasswordReset"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a href="{{ route('getLogin') }}" class="btn btn-secondary ml-2 btn-primary btn-frgt">Go Back</a>
                            <button type="submit" class="btn btn-primary">Set New Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Load FontAwesome -->
@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
@endpush

<!-- Script for toggling password visibility -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle for New Password field
        const togglePasswordReset = document.querySelector('#togglePasswordReset');
        const passwordReset = document.querySelector('#passwordReset');

        togglePasswordReset.addEventListener('click', function () {
            const type = passwordReset.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordReset.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash'); // Toggle the eye icon
        });

        // Toggle for Confirm Password field
        const toggleConfirmPasswordReset = document.querySelector('#toggleConfirmPasswordReset');
        const confirmPasswordReset = document.querySelector('#passwordConfirmationReset');

        toggleConfirmPasswordReset.addEventListener('click', function () {
            const type = confirmPasswordReset.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordReset.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash'); // Toggle the eye icon
        });
    });
</script>
@endpush
