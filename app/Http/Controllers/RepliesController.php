<?php

namespace App\Http\Controllers;

use Validator;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use App\Inspections\Spam;

class RepliesController extends Controller
{

    public function __construct()
    {
        return $this->middleware(['auth', 'email-must-be-confirmed'])->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $thread = Thread::find($request->threadId);

        if ($thread === null) {
            return response()->json([
                'state' => false,
                'message' => 'The thread you are replying to has been deleted.'
            ]);
        }

        if ($thread->locked) {
            return response("This thread has been locked", 423);
        }

        if (auth()->user()->can('createFrequent', new Reply()) === false) {
            return response()->json([
                'state' => false,
                'message' => 'You are posting too frequently. Please take a break. :)'
            ]);
        }

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

        $reply = $thread->addReply($request->replyBody);

        event(new \App\Events\ReplyEvent($reply, $thread, 'store'));

        /**
         * Tell if the writer of the reply is also the writer of the thread.
         * This condition will be used at client side to decide the right template
         * for the reply to be shown.
         */
        $isWriterOfThread = $reply->user_id === $thread->user_id;

        return response()->json([
            'state' => true,
            'replyId' => $reply->id,
            'replyBody' => $reply->body,
            'username' => $reply->user->username,
            'isWriterOfThread' => $isWriterOfThread,
            'replies_count' => $this->getRepliesCount($thread->id),
            'message' => 'Your reply has been published successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Spam $spam)
    {
        $reply = Reply::find(request('replyId'));

        if ($reply === null) {
            return response()->json([
                'state' => false,
                'message' => 'The reply you are editing is not exist anymore.'
            ]);
        }

        if (auth()->user()->can('update', $reply) === false) {
            return response()->json([
                'state' => false,
                'message' => 'You are not authorized to edit this reply.'
            ]);
        }

        $newReplyBody = request('replyBody');

        if (strlen($newReplyBody) == 0) {

            return response()->json([
                'state' => false,
                'message' => 'Reply body has no content!.'
            ]);
        }

        if ($spam->detect($newReplyBody)) {

            return response()->json([
                'state' => false,
                'message' => 'The reply contains spam!.'
            ]);
        }

        $reply->body = $newReplyBody;

        if ($reply->save()) {
            event(new \App\Events\ReplyEvent($reply, null, 'update'));
            return response()->json([
                'state' => true,
                'message' => 'The edits of the reply have been saved.'
            ]);
        } else {
            return response()->json([
                'state' => false,
                'message' => 'Some issue at the server prevents saving the new edits.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $reply = Reply::find(request('reply_id'));

        // $this->authorize('delete', $reply);

        if ($reply === null) {
            return response()->json([
                'state' => false,
                'message' => 'The reply you are deleting is not exist anymore.'
            ]);
        }

        if (auth()->user()->can('delete', $reply) === false) {
            return response()->json([
                'state' => false,
                'message' => 'You are not authorized to delete this reply.'
            ]);
        }

        if ($reply->delete()) {
            return response()->json([
                'state' => true,
                'message' => 'The reply has been deleted.',
                'replies_count' => $this->getRepliesCount($reply->thread_id)
            ]);
        } else {
            return response()->json([
                'state' => false,
                'message' => 'Some server issue prevents the deletion of this reply.',
                'replies_count' => $this->getRepliesCount($reply->thread_id)
            ]);
        }
    }

    private function getRepliesCount($threadId)
    {

        return Thread::find($threadId)->replies()->count();
    }

}
