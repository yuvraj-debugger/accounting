
  <!doctype html>
  <html lang="en">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Forgot Password</title>
      <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
      <link href="{{asset('assets/css/auth.css')}}" rel="stylesheet">
  </head>

  <body>
      <div class="wrapper">
          <div class="auth-content">

              <div class="card">
                  <div class="card-body text-center">
                      {{-- <div class="mb-4">
                          <img class="brand" src="assets/img/bootstraper-logo.png" alt="bootstraper logo">
                      </div> --}}
                      @if(Session::has('message'))
              <div class="alert alert-success" role="alert" id="message">

                  {{Session::get('message')}}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"  aria-label="Close"></button>
               @endif

                      <h6 class="mb-4 text-muted">Reset Password</h6>

                      <form action="{{ route('reset.password.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                            <div class="col-md-6">
                                <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>
                        <br/>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                            <div class="col-md-6">
                                <input type="password" id="password" class="form-control" name="password" required autofocus>
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                        </div>
                        <br/>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>
                            <div class="col-md-6">
                                <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required autofocus>
                                @if ($errors->has('password_confirmation'))
                                    <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                @endif
                            </div>
                        </div>

                        <br/>
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                Reset Password
                            </button>
                        </div>
                    </form>
                      <p class="mb-0 text-muted">Don’t have an account? <a href="{{route('register')}}">Sign up</a></p>
                  </div>
              </div>
          </div>
      </div>
      <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
      <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
  </body>
  <script>

  setTimeout(function() {
      $('#message').hide();
   }, 3000);


  </script>


  </html>
