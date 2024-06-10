<?php
use Illuminate\Support\Facades\Session;

?>



<!doctype html>
<!--
* Bootstrap Simple Admin Template
* Version: 2.1
* Author: Alexis Luna
* Website: https://github.com/alexis-luna/bootstrap-simple-admin-template
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sign up</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/auth.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="auth-content">
            <div class="card">
                <div class="card-body text-center">
                    {{-- <div class="mb-4">
                        <img class="brand" src="assets/img/bootstraper-logo.png" alt="bootstraper logo">
                    </div> --}}
                    <h6 class="mb-4 text-muted">Create new account</h6>
                    @if(Session::has('error'))
                    <p class="text-danger" >{{Session::get('error')}} </p>

                    @endif
                    <form action="{{route('register')}}" method="post">
                    @csrf
                        <div class="mb-3 text-start">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Name" required>
                            @if($errors->has('name'))
                            <p class="text-danger" >{{$errors->first('name')}} </p>
                            @endif
                        </div>
                        <div class="mb-3 text-start">
                            <label for="email" class="form-label">Email adress</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                            @if($errors->has('email'))
                            <p class="text-danger" >{{$errors->first('email')}} </p>
                            @endif
                        </div>
                        <div class="mb-3 text-start">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                            @if($errors->has('password'))
                            <p class="text-danger" >{{$errors->first('password')}} </p>
                            @endif
                        </div>
                        <div class="mb-3 text-start">
                            <label for="password" class="form-label">Confirmed Password</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Password" required>
                        </div>
                        <!--
                        <div class="mb-3">
                            <input type="password" class="form-control" placeholder="Confirm password" required>
                        </div>
                        -->
                        <div class="mb-3 text-start">
                            <div class="form-check">
                              <input class="form-check-input" name="confirm" type="checkbox" value="" id="check1">
                              <label class="form-check-label" for="check1">
                                I agree to the <a href="#" tabindex="-1">terms and policy</a>.
                              </label>
                            </div>
                        </div>
                        <button class="btn btn-primary shadow-2 mb-4">Register</button>
                    </form>
                    <p class="mb-0 text-muted">Allready have an account? <a href="/login">Log in</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
