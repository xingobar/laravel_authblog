@extends('layouts.app')

@section('meta')
    @if(Auth::user()) 
    <meta name="api-token" content="{{ Auth::user()->api_token }}">
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>

    @if($errors->has('constellation'))
        <div class="alert alert-danger" role="alert">
            {{$errors->first('constellation')}}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <ul>
                @foreach($constellations as $constellation)
                    <li>
                        <a href="/constellation/{{$constellation->id}}">{{$constellation->name}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

<!-- https: //stackoverflow.com/questions/43281009/laravel-5-4-api-route-401 -->
<script type="text/javascript">
    $(document).ready(function() {
       var api_token =  $('meta[name="api-token"]').attr('content');
       console.log(api_token)

       $.ajax({
           url:'/api/show/constellation/1',
           type:'post',
           headers: {
                'Authorization':'Bearer ' + api_token,
            },
           success:function(response) {
               console.log(response);
           },error:function(xhr,status,err) {
               console.log(err);
           }
       })
    })
</script>
