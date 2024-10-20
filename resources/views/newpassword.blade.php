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

                        <div class="form-floating mb-3">
                            <label for="email" class="entries">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" required placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <label for="password" class="entries">New Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required placeholder="New password">
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="togglePassword"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <label for="password_confirmation" class="entries">Confirm Password</label>
                            <div class="input-group">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm password">
                                <span class="input-group-text" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="toggleConfirmPassword"></i>
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
