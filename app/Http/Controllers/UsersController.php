<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ModelActivityEvent;
use App\User;
use App\Reply;
use App\Thread;
use App\Channel;
use App\Activity;

class UsersController extends Controller {

    public function __construct() {
        $this->middleware('auth')->except('index', 'show');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {

        $user_profile = $user;
        // $activities = $user->activities()->with('subject')->with('user')->latest()->paginate(10);
        $activitiesDays = Activity::feed($user_profile);

        return view('users.show', compact('user_profile', 'activitiesDays'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function storeLikeReplyToggle() {

        $replyId = request('reply_id');
        $reply = Reply::find($replyId);

        if ($reply !== null) {

            auth()->user()->likeReplyToggle($replyId);

            $count = $reply->usersLikes()->count();
            $was_it_like_or_unlike = $reply->isAlreadyLiked(); // Tell if it was like or unlick.

            $this->toggleLikeActivity($reply, $was_it_like_or_unlike);

            return response()->json([
                        'was_it_like_or_unlike' => $was_it_like_or_unlike,
                        'likesCount' => $count
            ]);
        }
    }

    protected function toggleLikeActivity($reply, $was_it_like_or_unlike) {
        if ($was_it_like_or_unlike) { // register activity of that like on reply.
            event(new ModelActivityEvent($reply, 'liked'));
        } else { // unregister activity due to removal of that like on reply.
            Activity::where('subject_id', $reply->id)
                    ->where('activity_type', 'liked')
                    ->where('user_id', auth()->id())
                    ->delete();
        }
    }

}
