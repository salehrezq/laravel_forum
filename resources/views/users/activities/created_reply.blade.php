@component('users.activities.activity')

@slot('head_left')
        Reply posted on thread: <a href="{{route('threads.show', 
                   ['channelSlug' => $itemActivity->thread_channel_slug_for_reply,
               'thread' => $itemActivity->thread_id_for_reply])}}">{{$itemActivity->thread_title_for_reply}}</a>
@endslot

@slot('head_right')
   {{$itemActivity->subject_created_at}}
@endslot

@slot('body')
 {{$itemActivity->replyBody}}
@endslot

@endcomponent