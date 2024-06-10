<?php
use Illuminate\Support\Facades\Session;

?><x-main>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    {{-- <div class="mb-4">
                        <img class="brand" src="assets/img/bootstraper-logo.png" alt="bootstraper logo">
                    </div> --}}




                    <form action="{{ route('profile-update') }}" method="POST">
                      @csrf

                      <input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
                      <div class="form-group row">
                          <label for="email_address" class="col-md-4 col-form-label text-md-right">Name</label>
                          <div class="col-md-6">
                              <input type="text" id="name" class="form-control" name="name" value="{{Auth::user()->name}}"required autofocus>
                              @if ($errors->has('name'))
                                  <span class="text-danger">{{ $errors->first('name') }}</span>
                              @endif
                          </div>
                      </div>
                      <br/>
                      <div class="form-group row">
                        <label for="email_address" class="col-md-4 col-form-label text-md-right">Email</label>
                        <div class="col-md-6">
                            <input type="text" id="email" class="form-control" name="email" value="{{! empty (Auth::user()->email) ?  Auth::user()->email : ''}}" required autofocus>
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>
                    <br/>
                    @if(Session::has('success'))
                    <p class="text-success"  id="success">{{Session::get('success')}} </p>
                    @endif
                    <br />
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </form>
                      <br/>

                </div>
            </div>
            </div>
        </div>
        <div class="col-md-12">
              <div class="card">
                <div class="card-body text-center">
            <form action="{{ route('update-password') }}" method="POST">

                @csrf
                <input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
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
                @if(Session::has('message'))
                <p class="text-success"  id="message">{{Session::get('message')}} </p>
                @endif

                <br/>
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            $('#success').hide();
         }, 3000);

         setTimeout(function() {
            $('#message').hide();
         }, 3000);

        </script>



        </x-main>
