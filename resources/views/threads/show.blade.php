@extends('layouts.app')

@section('content')
    <div class="container">
        @auth
            @can('subscribe', $thread)
                <input class='threadId' type="hidden" value="{{$thread->id}}">
                <div class="row">
                    <div class="col-md-1">
                        <button type="button"
                                class="btn btn-link btn_subscribe">{{$thread->was_this_thread_subscribed_to_by_auth_user? 'Unsubscribe from this thread' : 'Subscribe to this thread'}}</button>
                    </div>
                </div>
            @endcan
        @endauth
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header thread">
                        <img class="" height="42" width="42" src="{{ asset('storage/avatars/'. $avatar_path) }}" alt="">
                        {{$thread->title}}
                        &nbsp;<span class="threadby">by</span>
                        <a href="{{route('users.show', ['user' => $thread->user->username])}}">{{$thread->user->username}}</a>
                    </div>
                    <div class="card-body">
                        {{$thread->body}}
                    </div>
                </div>
                <hr>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p>This thread was created {{$thread->createdAtForHumans()}} by <a
                                    href="#">{{$thread->user->username}}</a>. It has <span
                                    id="replies_count">{{$thread->replies_count}}</span> <span
                                    id="replies_name">{{str_plural('reply', $thread->replies_count)}}</span></p>
                    </div>
                </div>
            </div>
        </div>
        @if(auth()->check())
            <div class="row">
                <div class="col-md-8">
                    <div class="card padding10">
                        <!-- Sent by xmlhttprequest instead -->
                        <div class="replyPublishArea">
                            <input type="hidden" name="threadId" class="threadId" value="{{$thread->id}}">
                            <div class="form-group">
                                <textarea rows="3" required name="replyBody" class="replyBodyTextArea form-control"
                                          placeholder="write a reply..."></textarea>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary btnReply">Reply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <p class="text-center">Please&nbsp;<a href="{{route('login')}}">sign in</a>&nbsp;to participate in this
                thread.</p>
        @endif
        <div class="repliesArea">
        @foreach ($replies as $reply)
            <!-- The following div element is used as template in JS file,
        so make sure to update BOTH when required -->
                <div class="row reply">
                    <div class="col-md-8" id='reply-{{$reply->id}}'>
                        <div class="card">
                            <div class="card-header level">
                                <div class="flex">
                                    By:&nbsp;<a
                                            href="{{route('users.show', ['user' => $reply->user_name])}}">{{$reply->user_name}}</a>&nbsp;&nbsp;|&nbsp;&nbsp;{{$reply->createdAtForHumans()}}
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
                                        <span>Likes:&nbsp;</span><span
                                                class="likesCounter">{{ $reply->users_likes_count }}</span>
                                        <input type="hidden" class='replyId' value="{{$reply->id}}">
                                        <span class='btn-span likeReplyBtnToggle'>{{ $reply->was_this_reply_liked_by_auth_user? 'Unlike' : 'Like' }}</span>
                                    </div>
                                @endif
                                @can('setBestReply', $thread)
                                    <div class="set-best-reply-area">
                                        <input type="hidden" class='replyId' value="{{$reply->id}}">
                                        <i title="Mark as best reply"
                                           class="fas fa-check best-reply-icon enabled"></i>
                                    </div>
                                @endcan
                            </div>
                            <div id="reply-container-{{$reply->id}}">
                                <div class="card-body" id="reply-body-{{$reply->id}}">
                                    {!! $reply->body !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{$replies->links()}}
    </div>
@endsection

