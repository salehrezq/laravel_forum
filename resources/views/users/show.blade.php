@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{$user_profile->name}}</h1>
    </div>
    @foreach ($activitiesDays as $dayActivity => $activities)
    <h2>{{$dayActivity}}</h2>
        @foreach ($activities as $itemActivity)
            @if($itemActivity->subject_type === 'App\\Thread')
                @include('users.activities.created_thread')
            @elseif($itemActivity->subject_type === 'App\\Reply')
                @include('users.activities.created_reply')
            @endif
        @endforeach
    @endforeach
</div>
@endsection