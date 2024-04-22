<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileDetailUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserDetailInfo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function show(Request $request): Response
    {
        $detail = UserDetailInfo::query()->where('user_id', Auth::id())->first();

        return Inertia::render('Profile/Detail', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'detail' => $detail,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();
        if ($request->has('email')) {
            // Проверяем, изменил ли пользователь email
            if ($user->email !== $validatedData['email']) {
                $validatedData['email_verified_at'] = null;
            }
        }

        $user->fill($validatedData);
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updateDetails(ProfileDetailUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        // Если пользователь загрузил новое изображение
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');

            if ($image->isValid()) {
                if ($user->userInfo->avatar) {
                    Storage::disk('public')->delete($user->userInfo->avatar);
                }

                $path = $image->store('uploads', 'public');
                $user->userInfo->avatar = $path;
                $user->userInfo->save();
                unset($validatedData['avatar']);
            }
        }

        if ($user->userInfo) {
            $user->userInfo->update($validatedData);
        } else {
            $user->userInfo()->create($validatedData);
        }

        return Redirect::route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
