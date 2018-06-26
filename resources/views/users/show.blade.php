@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{$user_profile->name}}</h1>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Threads</div>
                <div class="card-body">
                    @foreach ($user_threads as $thread)
                    <article>
                        <div class="level">
                            <h4 class="flex">
                                <a href="{{$thread->path()}}">{{$thread->title}}</a>
                            </h4>
                            <span>{{$thread->replies_count}}&nbsp;{{str_plural('comment', $thread->replies_count)}}</span>
                        </div>
                        <div class="body">{{$thread->body}}</div>
                        <hr>
                    </article>
                    @endforeach
                </div>

            </div>
            {{$user_threads->links()}}
        </div>
    </div>
</div>

@endsection