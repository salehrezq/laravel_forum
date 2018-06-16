@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header thread">{{$thread->title}}</div>
                <div class="card-body">
                    {{$thread->body}}
                </div>

            </div>
            <hr>
        </div>
    </div>

    @foreach ($replies as $reply)
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">By:&nbsp;{{$reply->user->name}}&nbsp;&nbsp;|&nbsp;&nbsp;{{$reply->createdAtForHumans()}}</div>
                <div class="card-body">
                    {{$reply->body}}
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection

