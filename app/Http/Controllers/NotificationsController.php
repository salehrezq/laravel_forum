<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller {

    public function __construct() {
        return $this->middleware('auth')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page) {

        $user = auth()->user();

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        if ($unreadNotificationsCount < 1) {
            return response()->json(['status' => false]);
        }

        $per_page = 6;

        $unreadNotificationsResults = $user->unreadNotifications()
                ->limit($per_page)
                ->offset(($page - 1) * $per_page)
                ->get(['id', 'data', 'created_at']);

        return response()->json([
                    'status' => true,

                    'unreadNotifications' => $unreadNotificationsResults,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    public function markAsRead(DatabaseNotification $notification) {
        
        if ($notification->unread()) {
            $notification->markAsRead();
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    public function markAllAsRead() {

        $result = auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        if ($result > 0) {
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
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
    public function show($id) {
        //
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

}
