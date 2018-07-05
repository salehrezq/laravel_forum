@component('users.activities.activity')

@slot('head_left')

{{-- url with anchor tag for this route:
  Route::get('/threads/{channelSlug}/{thread}', 'ThreadsController@show')->name('threads.show') --}}

Liked a reply on <a href='/threads/{{$itemActivity->thread_channel_slug_for_reply}}/{{$itemActivity->thread_id_for_reply}}/#reply-{{$itemActivity->subject_id}}'>
        {{$itemActivity->thread_title_for_reply}}
</a>

@endslot

@slot('head_right')
   {{$itemActivity->subject_created_at}}
@endslot

@slot('body')
 {{$itemActivity->replyBody}}
@endslot

@endcomponent