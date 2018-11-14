{{-- This is how the best reply tick is shown to the writer of the thread --}}
@can('setBestReply', $thread)
    <div class="set-best-reply-area">
        <input type="hidden" class='replyId' value="{{$reply->id}}">
        <i title="{{ $thread->best_reply === $reply->id? 'Was marked as the best reply': 'Mark as the best reply' }}"
           class="fas fa-check best-reply-icon enabled clickable {{ $thread->best_reply === $reply->id? 'best-reply-icon-selected': '' }}"></i>
    </div>
@endcan
{{-- This is how the best reply tick is shown to other authenticated users (other than the writer of the thread) --}}
@can('viewBestReply', $thread)
    @if($reply->id === $thread->best_reply)
        <div class="set-best-reply-area">
            <i title="This reply was set as the best reply by the writer of this thread"
               class="fas fa-check best-reply-icon {{ $thread->best_reply === $reply->id? 'best-reply-icon-selected': '' }}"></i>
        </div>
    @endif
@endcan
{{-- This is how the best reply tick is shown to the guests of the thread --}}
@guest
    @if($reply->id === $thread->best_reply)
        <div class="set-best-reply-area">
            <i title="This reply was set as the best reply by the writer of this thread"
               class="fas fa-check best-reply-icon best-reply-icon-selected"></i>
        </div>
    @endif
@endguest