<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
use App\Reply;

class BestRepliesController extends Controller
{
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
        $replyId = intval(request('replyId'));

        $reply = Reply::find($replyId);
        $thread = Thread::find($reply->thread_id);

        if (auth()->user()->can('markBestReply', $thread)) {

            if ($thread->best_reply === $replyId) {
                $state = $thread->unmarkBestReply();
                $mark = false;
            } else {
                $state = $thread->markBestReply($replyId);
                $mark = true;
            }

            if ($state) {
                return response()->json([
                    'state' => $state,
                    'markState' => $mark,
                ]);
            } else {
                return response()->json([
                    'state' => $state,
                    'message' => 'Some error happens at the server side, please try again.'
                ]);
            }
        } else {
            return response()->json([
                'state' => false,
                'message' => 'You are not authorized to do this action.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
