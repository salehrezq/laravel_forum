<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Str;

class SettingsController extends Controller
{

    public function __construct() {
        $this->middleware('auth')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        if (auth()->user()->can('view', $user))
        {
            $avatar_path = $user->avatar_path;

            if (isset($avatar_path) && !is_null($avatar_path)) {
                $avatar_path = basename($avatar_path);
            }else{
                $avatar_path = 'default-avatar.jpg';
            }
            return view('settings.show', [
                'username' => $user->username,
                'avatar_path' => $avatar_path]);
        }else{
            return redirect()->route('users.settings.get', auth()->user()->username);
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $file = $request->file('user_avatar');
        $user = auth()->user();

        $name =  Str::random(5).'_'.$user->username.'_'.$user->id.'.'.$file->guessClientExtension();

        $path = $file->storeAs('public/avatars', $name);

        $user->avatar_path = $path;
        $user->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
