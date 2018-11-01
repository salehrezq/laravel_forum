<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Mail\ConfirmationEmail;

class UsersEmailConfirmationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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
        //
    }

    /**
     * Confirm the authenticated user's email
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function confirmEmail($hash)
    {
        $user = User::find(auth()->id());

        if (auth()->user()->confirmation_hash === $hash) {
            $user->confirmed = true;
            $user->save();
            return back();
        } else {
            return view('auth.confirm-email.resend-conf-mail');
        }
    }

    /**
     * Resend the confirmation link to the authenticated user's email
     */
    public function confirmEmailResend()
    {
        $user = User::find(auth()->id());

        $user->confirmation_hash = str_random(60);
        $user->save();

        \Mail::to($user)->send(new ConfirmationEmail($user));
        return back();
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
