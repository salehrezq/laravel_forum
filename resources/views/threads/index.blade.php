@extends('layouts.app')

@section('content')
<div class="container">
    <div class="div-left">
        <a href="{{route('threads.create')}}">Create New Thread</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Threads</div>
                <div class="card-body">
                    @foreach ($threads as $thread)
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
        </div>
    </div>
</div>
@endsection
