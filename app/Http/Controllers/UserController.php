<?php

namespace App\Http\Controllers;

use App\Events\SendLikeEvent;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
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
    public function show(string $login): Response
    {
        $user = User::query()->where('login', $login)->with('detailInfo')->first();

        $services = Service::with('cover', 'images', 'categories', 'user.detailInfo')
            ->where('user_id', $user->id)
            ->get();

        return Inertia::render('User/User', [
            'user' => $user,
            'services' => ServiceResource::collection($services),
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
