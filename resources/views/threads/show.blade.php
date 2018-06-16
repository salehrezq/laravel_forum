@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{$thread->title}}</div>
                <div class="card-body">
                    {{$thread->body}}
                </div>
            </div>
        </div>
    </div>
    @foreach ($thread->replies as $reply)
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card"  style="margin-top: 10px">
                <div class="card-header card-header2">sdg</div>
                <div class="card-body">
                    {{$reply->body}}
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection

