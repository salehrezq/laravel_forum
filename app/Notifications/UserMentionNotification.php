<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;

class UserMentionNotification extends Notification {

    use Queueable;

    protected $replyId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($replyId) {
        $this->replyId = $replyId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
//    public function toMail($notifiable)
//    {
//        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
//    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable) {

        $notifiData = $this->extractData($this->replyId);

        return [
            'replyId' => $this->replyId,
            'author' => $notifiData->author,
            'thread_title' => $notifiData->thread_title,
            'thread_id' => $notifiData->thread_id,
            'thread_channel_slug' => $notifiData->thread_channel_slug,
        ];
    }

    private function extractData($replyId) {

        return \App\Reply::select('users.username as author', 'threads.id as thread_id', 'channels.slug as thread_channel_slug', DB::raw("CONCAT(LEFT(threads.title, 99), IF(LENGTH(threads.title)>99,'...','')) AS thread_title"))
                        ->join('users', 'users.id', '=', 'replies.user_id')
                        ->join('threads', 'threads.id', '=', 'replies.thread_id')
                        ->join('channels', 'channels.id', '=', 'threads.channel_id')
                        ->where('replies.id', $replyId)
                        ->first();
    }

}
