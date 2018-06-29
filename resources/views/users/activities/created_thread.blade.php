@component('users.activities.activity')

@slot('head_left')
 Thread posted: <a href="{{route('threads.show', 
                               ['channelSlug' => $itemActivity->channelSlug,
                           'thread' => $itemActivity->threadId])}}">{{$itemActivity->threadTitle}}</a>
@endslot

@slot('head_right')
     {{$itemActivity->subject_created_at}}
@endslot

@slot('body')
 {{$itemActivity->threadBody}}
@endslot

@endcomponent

