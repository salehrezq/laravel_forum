<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Helpers\ThreadsFilters;
use App\Inspections\Spam;
use App\Thread;
use App\Reply;
use App\Channel;

class ThreadsController extends Controller {

    public function __construct() {
        return $this->middleware('auth')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channelSlug = null) {

        $threadsFilters = new ThreadsFilters(request());

        if ($this->isFiltered($threadsFilters->queryfilters)) {
            $threads = Thread::filter($threadsFilters);
        } elseif (isset($channelSlug)) {
            $threads = $channelSlug->threads()->latest();
        } else {
            $threads = Thread::latest();
        }

        $threads = $threads->with('user')->with('channel')->paginate(25);

        return view('threads.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'threadTitle' => ['required', new \App\Rules\SpamFree()],
                    'threadBody' => ['required', new \App\Rules\SpamFree()],
                    'channelId' => 'required|exists:channels,id'
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $savedThread = Thread::create([
                    'user_id' => auth()->id(),
                    'channel_id' => $request->channelId,
                    'title' => $request->threadTitle,
                    'body' => $request->threadBody
        ]);

        return redirect($savedThread->path())->with('flash', 'Your thread has been published successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channelSlug, Thread $thread) {

        if ($thread->channel_id === $channelSlug->id) {

            $paginate = 5;

            if (auth()->check()) { // user:
                $user_id = auth()->id();

                $thread = Thread::select('threads.*', DB::raw("(exists(select * from subscriptions "
                                        . "where user_id = $user_id "
                                        . "and thread_id = $thread->id)) as was_this_thread_subscribed_to_by_auth_user"),
                                       DB::raw("(SELECT avatar_path FROM users INNER JOIN threads ON threads.user_id = users.id WHERE users.id = $thread->user_id limit 1) AS avatar_path"))
                        ->where('id', $thread->id)
                        ->first();

                $avatar_path = is_null($thread->avatar_path)? 'default-avatar.jpg' : basename($thread->avatar_path);

                $replies = Reply::select('replies.*', 'users.username as user_name', 'users.id as user_id', DB::raw("(select count(*) "
                                                . "from `users` inner join `likeables` "
                                                . "on `users`.`id` = `likeables`.`user_id` "
                                                . "where `replies`.`id` = `likeables`.`likeable_id` "
                                                . "and `likeables`.`likeable_type` = 'App\\\Reply') "
                                                . "as `users_likes_count`"), DB::raw("(exists(select * from `likeables` "
                                                . "where `user_id` = $user_id and `likeable_id` = `replies`.`id`)) "
                                                . "as `was_this_reply_liked_by_auth_user`"))
                                ->join('users', 'users.id', '=', 'replies.user_id')
                                ->where('thread_id', '=', $thread->id)
                                ->latest()->paginate($paginate);

                auth()->user()->readThread($thread->id);
            } else {//guest:
                $replies = Reply::select('replies.user_id', 'replies.body', 'replies.created_at', 'users.username as user_name')
                                ->join('users', 'replies.user_id', '=', 'users.id')
                                ->where('replies.thread_id', $thread->id)
                                ->latest()->paginate($paginate);
            }

            return view('threads.show', compact('thread', 'replies', 'channelSlug', 'avatar_path'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        $thread = Thread::find(request('thread_id'));

        // $this->authorize('delete', $thread);

        if ($thread !== null) {

            if (auth()->user()->can('delete', $thread)) {

                // Deletion of this thread also causes the database to delete
                // its associated replies using a database cascade delete constraint
                $is_deleted = $thread->delete();

                if ($is_deleted === true) {
                    $state = true;
                } else {
                    $state = false;
                }
                return response()->json(['state' => $state]);
            }
        }
    }

    private function isFiltered($queryFilters) {

        $requestQueryFilters = request()->all();

        foreach ($queryFilters as $queryFilter) {
            if (array_key_exists($queryFilter, $requestQueryFilters)) {
                return true;
            }
        }
        return false;
    }

}
