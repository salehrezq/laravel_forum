<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
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

        // Add query string values as needed
        $queryFilters = ['user_id'];

        if ($this->isFiltered($queryFilters)) {
            $threads = Thread::filter(new ThreadsFilters(request()))->get();
        } elseif (isset($channelSlug)) {
            $threads = $channelSlug->threads()->latest()->get();
        } else {
            $threads = Thread::latest()->get();
        }

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
            $replies = Reply::where('thread_id', $thread->id)->latest()->with('user')->get();
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
    public function destroy(Thread $thread) {
        //
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
