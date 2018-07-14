<?php

namespace App\Http\Controllers;

use Validator;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Http\Request;

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
                    'replyBody' => 'required',
                    'threadId' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                        'state' => false,
                        'message' => 'The reply body is required.'
            ]);
        }

        $thread = Thread::find($request->threadId);

        if ($thread !== null) {

            $reply = $thread->addReply($request->replyBody);

            \App\Subscription::notifySubscribers($reply);

            return response()->json([
                        'state' => true,
                        'replyId' => $reply->id,
                        'replyBody' => $reply->body,
                        'replyUserId' => $reply->user_id,
                        'username' => $reply->user->name,
                        'replies_count' => $this->getRepliesCount($thread->id),
                        'message' => 'Your reply has been published successfully.'
            ]);
        }

        return response()->json([
                    'state' => false,
                    'message' => 'The thread you are replying has been deleted.'
        ]);
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
    public function update() {

        $reply = Reply::find(request('replyId'));

        if ($reply !== null) {
            if (auth()->user()->can('update', $reply)) {

                $newReplyBody = request('replyBody');

                if (strlen($newReplyBody) != 0) {

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
