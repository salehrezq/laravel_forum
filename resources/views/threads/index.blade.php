@extends('layouts.app')

@section('content')
<div class="container">
    <div class="div-left">
        <a href="{{route('threads.create')}}">Create New Thread</a>
    </div>
    @forelse($threads as $thread)
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header level">
                    <div class="flex">
                        <a href="{{$thread->path()}}">{{$thread->title}}</a> by <a href="{{route('users.show', ['user' => $thread->user->id])}}">{{$thread->user->name}}</a>
                    </div>
                    {{$thread->replies_count}}&nbsp;{{str_plural('comment', $thread->replies_count)}}&nbsp;&nbsp;|&nbsp;&nbsp;{{$thread->createdAtForHumans()}}
                </div>
                <div class="card-body">
                    {{$thread->body}}
                </div>
            </div>
        </div>
    </div>
    @empty
    <p>There are no threads associated with this tag for the time being.</p>
    @endforelse
</div>
@endsection
