<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Mail\ConfirmationEmail;
use App\Http\Controllers\Controller;

class EmailConfirmationController extends Controller
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
        $user = auth()->user();

        if ($user->email_confirmed) {
            return view('auth.confirm-email.email-confirmed');
        }

        if ($user->confirmation_hash === $hash) {
            $user->email_confirmed = true;
            $user->save();
            return view('auth.confirm-email.email-confirmed');
        } else {
            return view('auth.confirm-email.resend-conf-mail');
        }
    }

    /**
     * Resend the confirmation link to the authenticated user's email
     */
    public function createConfirmEmailResend()
    {
        $user = auth()->user();

        if ($user->email_confirmed) {
            return view('auth.confirm-email.email-confirmed');
        }

        return view('auth.confirm-email.resend-conf-mail');
    }

    /**
     * Resend the confirmation link to the authenticated user's email
     */
    public function storeConfirmEmailResend()
    {
        $user = auth()->user();

        if ($user->email_confirmed) {
            return view('auth.confirm-email.email-confirmed');
        }

        $user->confirmation_hash = str_random(60);
        $user->save();

        \Mail::to($user)->send(new ConfirmationEmail($user));
        return back()->with('confirm-email-sent-flash', 'A new confirmation link has been sent; check your email.');
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
