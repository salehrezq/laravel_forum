<?php

namespace App\Policies;

use App\User;
use App\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the thread.
     *
     * @param  \App\User $user
     * @param  \App\Thread $thread
     * @return mixed
     */
    public function view(User $user, Thread $thread)
    {
        //
    }

    /**
     * Determine whether the user can create threads.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the thread.
     *
     * @param  \App\User $user
     * @param  \App\Thread $thread
     * @return mixed
     */
    public function update(User $user, Thread $thread)
    {
        return ($user->id === $thread->user_id) && !$thread->locked;
    }

    /**
     * Determine whether the user can delete the thread.
     *
     * @param  \App\User $user
     * @param  \App\Thread $thread
     * @return mixed
     */
    public function delete(User $user, Thread $thread)
    {

        if ($thread->user_id === $user->id) {
            return true;
        }
    }

    public function subscribe(User $user, Thread $thread)
    {
        if ($thread->user_id !== $user->id) {
            return true;
        }
    }

    public function markBestReply(User $user, Thread $thread)
    {
        return $thread->user_id === $user->id;
    }

    /**
     * @param User $user
     * @param Thread $thread
     * @return bool
     *
     * Rule for showing the best reply for other authenticated users,
     * other than the writer of this $thread.
     *
     */
    public function viewBestReply(User $user, Thread $thread)
    {
        return ($thread->user_id !== $user->id);
    }

}
