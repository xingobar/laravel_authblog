@extends('layouts.app')

@section('script')
    <script src="https://sdk.accountkit.com/zh_TW/sdk.js"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>

                        <div class="text-center">
                            <p>其他登入方式</p>
                            <a href="{{route('social.redirect','google')}}" class="btn btn-lg btn-danger btn-block">Google</a>
                            <a href="{{route('social.redirect','facebook')}}" class="btn btn-lg btn-success btn-block">Facebook</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <input value="+1" id="country_code" />
            <input placeholder="phone number" id="phone_number"/>
            <button onclick="smsLogin();">Login via SMS</button>
            <div>OR</div>
            <input placeholder="email" id="account_kit_email"/>
            <button onclick="emailLogin();">Login via Email</button>
        </div>
    </div>
</div>
@endsection

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
  // initialize Account Kit with CSRF protection

 $(document).ready(function() {
     var csrf_token = $('meta[name="csrf-token"]').attr('content');
  AccountKit_OnInteractive = function(){
    AccountKit.init(
      {
        appId:"406236216806661", 
        state: csrf_token, 
        version:"v1.1",
        fbAppEventsEnabled:true,
        redirect:"/"
      }
    );
  };
 })

  // login callback
  function loginCallback(response) {
    if (response.status === "PARTIALLY_AUTHENTICATED") {
      var code = response.code;
      var csrf = response.state;
      // Send code to server to exchange for access token
      console.log('receive access token');
      console.log(response);

      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
     });

      $.ajax({
          url: '/account_kit/login/success',
          type:'post',
          data: {
              code:code,
              csrf:csrf
          },
          success:function(response) {
              console.log(response);
          },error:function(){
              console.log('error')
          }
      })
    }
    else if (response.status === "NOT_AUTHENTICATED") {
      // handle authentication failure
      console.log('authenticate failure');
    }
    else if (response.status === "BAD_PARAMS") {
      // handle bad parameters
      console.log('bad parameters');
    }
  }

  // phone form submission handler
  function smsLogin() {
    var countryCode = document.getElementById("country_code").value;
    var phoneNumber = document.getElementById("phone_number").value;
    AccountKit.login(
      'PHONE', 
      {countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
      loginCallback
    );
  }


  // email form submission handler
  function emailLogin() {
    var emailAddress = document.getElementById("account_kit_email").value;
    AccountKit.login(
      'EMAIL',
      {emailAddress: emailAddress},
      loginCallback
    );
  }

 
</script>

