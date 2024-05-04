<?php

namespace App\Http\Controllers;

use App\Events\SendLikeEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //test
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
//    public function show(string $login): Response
//    {
//        $user = User::query()->where('login', $login)->with('detailInfo')->first();
//        return Inertia::render('User/User', [
//            'user' => $user,
//        ]);
//    }

    public function show(User $user)
    {
        return Inertia::render('User/Show', compact('user'));
    }

    public function sendLike(User $user, Request $request)
    {
        $data = $request->validate([
            'from_id' => 'required',
        ]);

        $like_str = 'Your like from with id '.$data['from_id'];

        broadcast(new SendLikeEvent($like_str, $user->id))->toOthers();

        return response()->json([
            'like_str' => $like_str
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
