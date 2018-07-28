<?php

namespace App\Http\Controllers;

use Validator;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use App\Inspections\Spam;

class RepliesController extends Controller {

    public function __construct() {
        return $this->middleware('auth')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'replyBody' => 'required|spamfree',
                    'threadId' => 'required',
        ]);

        if ($validator->fails()) {

            $failedRules = $validator->failed();

            if (isset($failedRules['replyBody']['Required'])) {
                $message = 'The reply body is required.';
            } else if (isset($failedRules['replyBody']['Spamfree'])) {
                $message = 'The reply contains spam!.';
            } else {
                $message = 'Unknown error detected at the server.';
            }

            return response()->json([
                        'state' => false,
                        'message' => $message
            ]);
        }

        $thread = Thread::find($request->threadId);

        if ($thread === null) {
            return response()->json([
                        'state' => false,
                        'message' => 'The thread you are replying to has been deleted.'
            ]);
        }

        if (!auth()->user()->can('createFrequent', new Reply())) {
            return response()->json([
                        'state' => false,
                        'message' => 'You are posting too frequently. Please take a break. :)'
            ]);
        }

        $reply = $thread->addReply($request->replyBody);

        \App\Subscription::notifySubscribers($thread, $reply);

        $this->metionUsers($reply);

        return response()->json([
                    'state' => true,
                    'replyId' => $reply->id,
                    'replyBody' => $reply->body,
                    'replyUserId' => $reply->user_id,
                    'username' => $reply->user->username,
                    'replies_count' => $this->getRepliesCount($thread->id),
                    'message' => 'Your reply has been published successfully.'
        ]);
    }

    protected function metionUsers($reply) {

        preg_match_all("/(?<=[^\w.-]|^)@([A-Za-z_\d]+(?:\.\w+)*)/", $reply->body, $matches);

        $usernames = $this->getUsernames($matches);

        if (count($usernames) > 0) {
            $usersIds = User::whereIn('username', $usernames)->get(['id']);
            $users = User::whereIn('id', $usersIds)->get();
            Notification::send($users, new \App\Notifications\UserMentionNotification($reply->id));
        }
    }

    private function getUsernames($matches) {

        $length = count($matches[1]);

        $usernames = [];

        for ($i = 0; $i < $length; $i++) {

            $username = $matches[1][$i];

            if ($username === auth()->user()->username) {
                continue;
            }

            $usernames[] = $username;
        }

        return $usernames;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $reply) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Spam $spam) {

        $reply = Reply::find(request('replyId'));

        if ($reply !== null) {
            if (auth()->user()->can('update', $reply)) {

                $newReplyBody = request('replyBody');

                if (strlen($newReplyBody) != 0) {

                    if ($spam->detect($newReplyBody)) {

                        return response()->json([
                                    'state' => false,
                                    'message' => 'The reply contains spam!.'
                        ]);
                    }

                    $reply->body = $newReplyBody;

                    if ($reply->save() !== null) {
                        $state = true;
                    } else {
                        $state = false;
                    }
                    return response()->json(['state' => $state]);
                }
            }
        }
        return response()->json(['state' => false]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        $reply = Reply::find(request('reply_id'));

        // $this->authorize('delete', $reply);

        if ($reply !== null) {

            if (auth()->user()->can('delete', $reply)) {

                if ($reply->delete() === true) {
                    $state = true;
                } else {
                    $state = false;
                }
                return response()->json([
                            'state' => $state,
                            'replies_count' => $this->getRepliesCount($reply->thread_id)
                ]);
            }
        }
    }

    private function getRepliesCount($threadId) {

        return Thread::find($threadId)->replies()->count();
    }

}
