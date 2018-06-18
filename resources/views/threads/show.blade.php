@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header thread">{{$thread->title}}&nbsp;&nbsp;<span class="threadby">by</span>&nbsp;&nbsp;{{$thread->user->name}}</div>
                <div class="card-body">
                    {{$thread->body}}
                </div>
            </div>
            <hr>
        </div>
    </div>
    @if(auth()->check())
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card thread-reply-form">
                <form method="POST" action="{{route('thread.replies', ['channel' => $channel, 'thread' => $thread->id])}}">
                    @csrf
                    <div class="form-group">
                        <textarea rows="3" name="replyBody" class="form-control" placeholder="write a reply..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Reply</button>
                </form>
            </div>
        </div>
    </div>
    @else
    <p class="text-center">Please&nbsp;<a href="{{route('login')}}">sign in</a>&nbsp;to participate in this thread.</p>
    @endif
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

