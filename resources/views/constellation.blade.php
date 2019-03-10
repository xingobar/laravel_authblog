@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>{{date('Y-m-d')}}</h4>
                @foreach($detail as $row)
                    <p>
                        <h4>
                            {{$row->constellation_luckies->title}}
                            {{$row->luck_star}}
                        </h4>
                        {{$row->description}}
                    </p>
                @endforeach
            </div>
        </div>
    </div>
@endsection
