<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Helpers\ThreadsFilters;
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

        $threads = $threads->with('user')->get();

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
                    'threadTitle' => 'required',
                    'threadBody' => 'required',
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

        return redirect($savedThread->path());
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
                $replies = Reply::select('replies.*', DB::raw("(select count(*) "
                                                . "from `users` inner join `likeables` "
                                                . "on `users`.`id` = `likeables`.`user_id` "
                                                . "where `replies`.`id` = `likeables`.`likeable_id` "
                                                . "and `likeables`.`likeable_type` = 'App\\\Reply') "
                                                . "as `users_likes_count`"), DB::raw("(select exists(select * from `likeables` "
                                                . "where `user_id` = $user_id and `likeable_id` = `replies`.`id`)) "
                                                . "as `was_this_reply_liked_by_auth_user`"))
                                ->from(DB::raw("`replies` where `thread_id` = $thread->id"))->latest()->paginate($paginate);
            } else {//guest:
                $replies = Reply::where('thread_id', $thread->id)->latest()->paginate($paginate);
            }

            return view('threads.show', compact('thread', 'replies', 'channelSlug'));
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

        if (auth()->user()->can('delete', $thread)) {

            $is_deleted = $thread->delete();

            if ($is_deleted === true) {
                $state = true;
            } else {
                $state = false;
            }
            return response()->json(['state' => $state]);
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
