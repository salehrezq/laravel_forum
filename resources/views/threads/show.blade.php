@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header thread">{{$thread->title}}&nbsp;&nbsp;<span class="threadby">by</span>&nbsp;&nbsp;<a href="{{route('users.show', ['user' => $thread->user->id])}}">{{$thread->user->name}}</a></div>
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
            <div class="card padding10">
                <form method="POST" action="{{route('thread.replies', ['channelSlug' => $channelSlug, 'thread' => $thread->id])}}">
                    @csrf
                    <div class="form-group">
                        <textarea rows="3" name="replyBody" class="form-control" placeholder="write a reply..."></textarea>
                    </div>
                    <div class="form-group">
                    <button type="submit" class="btn btn-primary">Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    <p class="text-center">Please&nbsp;<a href="{{route('login')}}">sign in</a>&nbsp;to participate in this thread.</p>
    @endif
    @foreach ($replies as $reply)
    <div class="row reply">
        <div class="col-md-8" id='reply-{{$reply->id}}'>
            <div class="card">
                <div class="card-header level">
                    <div class="flex">
                        By:&nbsp;<a href="{{route('users.show', ['user' => $reply->user_id])}}">{{$reply->user_name}}</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{$reply->createdAtForHumans()}}
                        @can('delete', $reply)
                            <div class='deleteReplyArea inline'>
                            <input type="hidden" class='replyId' value="{{$reply->id}}">
                            &nbsp;<span class='btn-span deleteReplyBtn'>Delete</span>
                            </div>
                        @endcan
                        @can('update', $reply)
                            <!-- The editing is done through JavaScript -->
                            <div class='editReplyMode inline'>
                            <input type="hidden" class='replyId' value="{{$reply->id}}">
                            &nbsp;<span class='btn-span editReplyBtn'>Edit</span>
                            </div>
                        @endcan
                    </div>
                    @if(auth()->check())
                    <div class='likeArea'>
                        <span class="likesCounter">{{ $reply->users_likes_count }}</span>
                        <input type="hidden" class='replyId' value="{{$reply->id}}">
                        <span class='btn-span likeReplyBtnToggle'>{{ $reply->was_this_reply_liked_by_auth_user? 'Unlike' : 'Like' }}</span>
                    </div>
                    @endif
                </div>
                <div id="reply-container-{{$reply->id}}">
                    <div class="card-body" id="reply-body-{{$reply->id}}">
                        {{$reply->body}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {{$replies->links()}}
</div>
@endsection

