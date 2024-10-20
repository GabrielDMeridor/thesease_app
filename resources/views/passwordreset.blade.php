@extends('guest-layout')

@section('content-header1')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card card-container">
                    <img class="card-img-top" src="{{ asset('img/ps.png') }}" style="width:auto;height:200px;">
                    <div class="centered">Reset Password</div>
                </div>
                <div class="card-body">
                    <!-- Display Success Messages -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Display Error Messages -->
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Password Reset Form -->
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="form-floating mb-3">
                            <label for="email" class="entries">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" required placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a href="{{ route('getLogin') }}" class="btn btn-secondary ml-2 btn-primary btn-frgt">Go Back</a>
                            <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
