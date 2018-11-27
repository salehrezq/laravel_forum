@extends('layouts.app')

@section('content')
    <div class="container">
        @auth
            <input class='threadId' type="hidden" value="{{$thread->id}}">
            <div class="row">
                <div class="col-md-6">
                    @can('subscribe', $thread)
                        <button type="button"
                                class="btn btn_subscribe">{{$thread->was_this_thread_subscribed_to_by_auth_user? 'Unsubscribe from this thread' : 'Subscribe to this thread'}}
                        </button>
                    @endcan
                    @if(auth()->user()->isAdmin())
                        <form class="inline" action="{{ route("locked-thread.store") }}" method="POST">
                            @csrf
                            <input type="hidden" name="threadId" value="{{ $thread->id }}">
                            <button type="submit"
                                    name="lock_thread"
                                    class="btn">{{ $thread->locked? 'Unlock' : 'Lock' }}
                            </button>
                        </form>
                    @endif
                    @if(auth()->user()->isNotAdmin() and $thread->locked)
                        <h3 class="inline"><span class="locked-thread-label">Locked thread</span></h3>
                    @endif
                    @can('update', $thread)
                        <button type="button"
                                class="btn btn-edit-thread">Edit
                        </button>
                    @endcan
                </div>
            </div>
        @endauth
        <div class="row">
            <div class="col-md-8 thread-container">
                <div class="card">
                    <div class="card-header thread-header">
                        <div class="header-content">
                            <img class="" height="42" width="42" src="{{ asset('storage/avatars/'. $avatar_path) }}"
                                 alt="">
                            <span class="thread-title">{{$thread->title}}</span>
                            &nbsp;<span class="threadby">by</span>
                            <a href="{{route('users.show', ['user' => $thread->user->username])}}">{{$thread->user->username}}</a>
                        </div>
                    </div>
                    <div class="card-body thread-body">
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
            @if(auth()->user()->email_confirmed and !$thread->locked)
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
                {{-- else of if(auth()->user()->email_confirmed and !$thread->locked) --}}
            @elseif((auth()->user()->email_confirmed === false) and !$thread->locked)
                <div class="row">
                    <div class="col-8">
                        <p class="text-center">To be able to comment on threads, go to your email and click on the
                            confirmation link from us to confirm your email, or resend a new email with a new
                            confirmation link from <a href="{{route('confirm.user.email.resend.get')}}">here</a>.</p>
                    </div>
                </div>
            @endif {{-- end of if(auth()->user()->email_confirmed and !$thread->locked) --}}
        @else {{-- else if(auth()->check()) --}}
        <div class="row">
            <div class="col-8">
                <p class="text-center">Please&nbsp;<a href="{{route('login')}}">sign in</a>&nbsp;to participate in
                    this
                    thread.</p>
            </div>
        </div>
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
                                @guest
                                    <div class='likeArea'>
                                        <span>Likes:&nbsp;</span><span
                                                class="likesCounter">{{ $reply->users_likes_count }}</span>
                                    </div>
                                @endguest
                                @include('threads.templates.show-best-reply-mark')
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

