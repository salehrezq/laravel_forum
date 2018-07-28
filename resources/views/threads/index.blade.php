@extends('layouts.app')

@section('content')
<div class="container">
    <div class="div-left">
        <a href="{{route('threads.create')}}">Create New Thread</a>
    </div>
    @forelse($threads as $thread)
    <div class="row justify-content-center thread">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header level">
                    <div class="flex">
                        <a href="{{$thread->path()}}">
                            @if((auth()->check()) and (auth()->user()->hasNotSeenLatestUpdatesFor($thread)))
                                <strong>{{$thread->title}}</strong>
                            @else
                                {{$thread->title}}
                            @endif
                        </a> by <a href="{{route('users.show', ['user' => $thread->user->id])}}">{{$thread->user->username}}</a>
                    </div>
                    {{$thread->replies_count}}&nbsp;{{str_plural('comment', $thread->replies_count)}}&nbsp;&nbsp;|&nbsp;&nbsp;{{$thread->createdAtForHumans()}}
                    @can('delete', $thread)
                    <div class='deleteThreadArea'>
                    <input type="hidden" class='threadId' value="{{$thread->id}}">
                    &nbsp;<span class='btn-span deleteThreadBtn'>Delete</span>
                    </div>
                    @endcan
                </div>
                <div class="card-body">
                    {{$thread->body}}
                </div>
            </div>
        </div>
    </div>
    @empty
    <p>There are no threads for the time being.</p>
    @endforelse
</div>
@endsection
