@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header thread">{{$thread->title}}&nbsp;&nbsp;<span class="threadby">by</span>&nbsp;&nbsp;{{$thread->user->name}}</div>
                <div class="card-body">
                    {{$thread->body}}
                </div>
            </div>
            <hr>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <p>This thread was created {{$thread->createdAtForHumans()}} by <a href="#">{{$thread->user->name}}</a>. It has {{$thread->replies_count}} {{str_plural('comment', $thread->replies_count)}}</p>
                </div>
            </div>
        </div>
    </div>
    @if(auth()->check())
    <div class="row">
        <div class="col-md-8">
            <div class="card thread-reply-form">
                <form method="POST" action="{{route('thread.replies', ['channelSlug' => $channelSlug, 'thread' => $thread->id])}}">
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
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header level">
                    <div class="flex">
                        By:&nbsp;<a href="#">{{$reply->user->name}}</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{$reply->createdAtForHumans()}}
                    </div>
                    @if(auth()->check())
                    <div class='likeArea'>
                        <span class="likesCounter">{{ $reply->users_likes_count }}</span>
                        <input type="hidden" class='replyId' value="{{$reply->id}}">
                        <span class='likeReplyBtnToggle'>{{ $reply->was_this_reply_liked_by_auth_user? 'Unlike' : 'Like' }}</span>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    {{$reply->body}}
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {{$replies->links()}}
</div>
@endsection

