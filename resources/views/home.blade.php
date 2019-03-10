@extends('layouts.app')

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
