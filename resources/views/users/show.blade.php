@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{$user_profile->name}}</h1>
    </div>
    @forelse ($activitiesDays as $dayActivity => $activities)
    <h2>{{$dayActivity}}</h2>
        @foreach ($activities as $itemActivity)
            @if($itemActivity->subject_type === 'App\\Thread')
                @include('users.activities.created_thread')
            @elseif(($itemActivity->subject_type === 'App\\Reply') && ($itemActivity->activity_type === 'created'))
                @include('users.activities.created_reply')
            @elseif(($itemActivity->subject_type === 'App\\Reply') && ($itemActivity->activity_type === 'liked'))
                @include('users.activities.liked_reply')
            @endif
        @endforeach
    @empty
    <p>There are no activities for this user yet.</p>
    @endforelse
</div>
@endsection