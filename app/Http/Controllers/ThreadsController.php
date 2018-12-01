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

class ThreadsController extends Controller
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
    public function index(Channel $channelSlug = null)
    {

        $threadsFilters = new ThreadsFilters(request());

        if ($this->isFiltered($threadsFilters->queryfilters)) {
            $threads = Thread::filter($threadsFilters);
        } elseif (isset($channelSlug)) {
            $threads = $channelSlug->threads()->latest();
        } else {
            $threads = Thread::latest();
        }

        $threads = $threads->with('user')->with('channel')->paginate(25);

        $trendingThreads = $this->getTrendingThreads();

        return view('threads.index', compact('threads', 'trendingThreads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'threadTitle' => ['required', new \App\Rules\SpamFree()],
            'threadBody' => ['required', new \App\Rules\SpamFree()],
            'channelId' => 'required|exists:channels,id',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha()]
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $savedThread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => $request->channelId,
            'title' => $request->threadTitle,
            'title_slug' => $request->threadTitle,
            'body' => $request->threadBody
        ]);

        return redirect($savedThread->path())->with('flash', 'Your thread has been published successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channelSlug, Thread $thread)
    {

        $thread->increment('views');

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

                $avatar_path = is_null($thread->avatar_path) ? 'default-avatar.jpg' : basename($thread->avatar_path);

                $replies = Reply::select('replies.*',
                    'users.username as user_name',
                    'users.id as user_id',
                    DB::raw("(select count(*) "
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
                $replies = Reply::select('replies.id',
                    'replies.user_id',
                    'replies.body',
                    'replies.created_at',
                    'users.username as user_name',
                    DB::raw("(select count(*) "
                        . "from `users` inner join `likeables` "
                        . "on `users`.`id` = `likeables`.`user_id` "
                        . "where `replies`.`id` = `likeables`.`likeable_id` "
                        . "and `likeables`.`likeable_type` = 'App\\\Reply') "
                        . "as `users_likes_count`"))
                    ->join('users', 'replies.user_id', '=', 'users.id')
                    ->where('replies.thread_id', $thread->id)
                    ->latest()->paginate($paginate);

                $avatar_path = $thread->user->avatar_path;
                $avatar_path = is_null($avatar_path) ? 'default-avatar.jpg' : basename($avatar_path);
            }

            return view('threads.show', compact('thread', 'replies', 'channelSlug', 'avatar_path'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $patchData = json_decode(request('patchData'));

        $validator = Validator::make((array)$patchData, [
            'threadId' => ['required', 'integer'],
            'threadTitle' => [new \App\Rules\SpamFree()],
            'threadBody' => [new \App\Rules\SpamFree()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state' => false,
                'status' => 432, // invented status code indicates validation errors
                'message' => $validator->messages()
            ]);
        }

        $thread = Thread::find($patchData->threadId);

        if ($thread !== null) {

            if (auth()->user()->can('update', $thread)) {

                if (!empty($patchData->threadTitle)) {
                    $thread->title = $patchData->threadTitle;
                }

                if (!empty($patchData->threadBody)) {
                    $thread->body = $patchData->threadBody;
                }
                if ($thread->save()) {
                    return response()->json([
                        'state' => true,
                        'message' => 'The thread has been updated.'
                    ]);
                } else {
                    return response()->json([
                        'state' => false,
                        'message' => 'The thread has NOT been updated due to some server issue. Try again.'
                    ]);
                }
            } else {
                return response()->json([
                    'state' => false,
                    'message' => 'You are not authorized to edit this thread.'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {

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

    private function isFiltered($queryFilters)
    {

        $requestQueryFilters = request()->all();

        foreach ($queryFilters as $queryFilter) {
            if (array_key_exists($queryFilter, $requestQueryFilters)) {
                return true;
            }
        }
        return false;
    }

    private function getTrendingThreads()
    {
        return Thread::where('views', '>', 0)
            ->with('channel')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();
    }

}
