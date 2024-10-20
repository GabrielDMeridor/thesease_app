<!DOCTYPE html>
<html lang="en">
 
<head>
 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
 
    <title>SB Admin 2 - Login</title>
 
    <!-- Custom fonts for this template-->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
 
    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

    <style>
        body {
            background-image: url("{{ asset('img/loginbg.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh; /* Makes sure it covers the full height of the viewport */
            width: 100vw; /* Makes sure it covers the full width */
            margin: 0;
            padding: 0;
        }

        /* Base styles */
.card-img-overflow {
    position: relative;
    top: -40px; /* Move the image upward */
    left: -15px; /* Slight adjustment */
    width: 230px; /* Adjust the size */
}

.rownames {
    margin-bottom: -100px;
    margin-top: -57px;
}

.welcome {
    padding-top: 100px;
    padding-left: 70px;
    color: #5E6797;
    font-size: 20px;
}

/* Responsive styles */
@media (max-width: 768px) {
    /* Smaller screens (Tablets, Mobile landscape) */

    .welcome {
        padding-top: 80px; /* Adjust top padding */
        padding-left: 20px; /* Reduce left padding */
        font-size: 16px; /* Slightly smaller text */
    }

    .card-img-overflow {
        top: -20px; /* Adjust image position */
        left: 0px; /* Center the image */
        width: 180px; /* Adjust image size */
    }

    .rownames {
        margin-bottom: -50px; /* Adjust margins */
        margin-top: -30px;
    }
}

@media (max-width: 576px) {
    /* Mobile screens (portrait) */

    .welcome {
        padding-top: 60px;
        padding-left: 10px; /* Further reduce padding */
        text-align: center; /* Center text on mobile */
        font-size: 15px;
    }

    .card-img-overflow {
        top: 0px; /* Reset position */
        left: 0px; /* Center image */
        width: 150px; /* Smaller image size */
        display: block;
        margin: 0 auto; /* Center image in column */
    }

    .rownames {
        margin-bottom: -30px;
        margin-top: -10px;
    }

    .rownames .col-sm {
        text-align: center; /* Center both image and text */
    }
}
        
    </style>

 
</head>
 
<body class="bg-gradient-primary">
 
    <div class="container">
    <br>
        <br>
        <br>
 
        <!-- Outer Row -->
        <div class="row justify-content-center">
 
            <div class="col-xl-5 col-lg-12 col-md-9">
 
                <div class="card border-0 shadow-lg my-5">
                <div class="row rownames">
                            <div class="col-sm ">
                            <p class="welcome">Welcome to Thesease</p>
                            </div>
                            <div class="col-sm">
                            <img src="{{ asset('img/gslogo.png') }}" class="card-img-overflow" alt="Overflowing Image">
                            </div>
                        </div>
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                    @if(session('error'))
                                    <div class="text-danger text-center">{{session('error')}}</div>
                                    @endif
                                    @if(session('success'))
                                    <div class="text-success text-center">{{session('success')}}</div>
                                    @endif

                                    <hr class="lineborder">
                                    </div>
 
                                    <form class="user" action="{{route('postSALogin')}}" method="post">
                                    @csrf
                                        <div class="form-group">
                                        <p class="credential-text">Enter your credentials</p>
                                            <input name="email" type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Email Address">
                                                @error('email')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                        </div>
                                        <div class="form-floating mb-3">
                                            <label for="password" class="entries">Password</label>
                                            <div class="input-group">
                                                <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required/>
                                                <span class="input-group-text" style="cursor: pointer;">
                                                    <i class="fas fa-eye" id="togglePassword"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="row pt-1">
                                                <div class="col">
                                                <a class="btn btn-primary btn-user btn-block btn-frgt" href="{{ route('password.request') }}">Reset Password</a>
                                                
                                                </div>
                                                <div class="col">
                                                <button type="submit" class="btn btn-primary btn-user btn-block">Log In</button>
                                                </div>
                                            </div>
                                    </form>
                                    <br>
                                    <hr class="lineborder">
                                    <div class="text-center">
                                    <a class="small btn btn-primary btn-user btn-block btn-createacc" href="{{ route('getSARegister') }}">Create an Account</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
        </div>
 
    </div>
 
    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
 
    <!-- Core plugin JavaScript-->
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>
 
    <!-- Custom scripts for all pages-->
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye icon
        this.classList.toggle('fa-eye-slash');
    });
    </script>
 
</body>
</html>