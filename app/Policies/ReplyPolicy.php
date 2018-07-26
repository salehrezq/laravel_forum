<?php

namespace App\Policies;

use App\User;
use App\Reply;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy {

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the reply.
     *
     * @param  \App\User  $user
     * @param  \App\Reply  $reply
     * @return mixed
     */
    public function view(User $user, Reply $reply) {
        //
    }

    /**
     * Determine whether the user can create replies.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user) {
        //
    }

    /**
     * Allow the user to create a new reply only if one minute has passed since
     * his latest/previous reply. If no previous reply exist allow him anyway.
     * 
     * @param User $user
     * @return boolean
     */
    public function createFrequent(User $user) {

        $lastReply = $user->latestReply();

        if ($lastReply !== null) {
            return !$lastReply->wasJustPublished();
        }

        return true;
    }

    /**
     * Determine whether the user can update the reply.
     *
     * @param  \App\User  $user
     * @param  \App\Reply  $reply
     * @return mixed
     */
    public function update(User $user, Reply $reply) {
        if ($reply->user_id === $user->id) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the reply.
     *
     * @param  \App\User  $user
     * @param  \App\Reply  $reply
     * @return mixed
     */
    public function delete(User $user, Reply $reply) {

        if ($reply->user_id === $user->id) {
            return true;
        }
    }

}
