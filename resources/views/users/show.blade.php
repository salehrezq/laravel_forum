@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{$user_profile->name}}</h1>
    </div>
    @foreach ($user_threads as $thread)
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header level">
                    <div class="flex">
                        <a href="{{$thread->path()}}">{{$thread->title}}</a>
                    </div>
                    {{$thread->replies_count}}&nbsp;{{str_plural('comment', $thread->replies_count)}}&nbsp;|&nbsp;{{$thread->createdAtForHumans()}}
                </div>
                <div class="card-body">
                    {{$thread->body}}
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {{$user_threads->links()}}
</div>

@endsection