<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\CloudinaryMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProfileController extends Controller
{
    public function __construct(private CloudinaryMediaService $media) {}

    public function edit(): View
    {
        return view('waiter.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->except(['avatar']);

        if ($request->hasFile('avatar')) {
            try {
                $this->media->delete($user->avatar_public_id);
                $upload = $this->media->upload($request->file('avatar'), 'avatars');
                $data['avatar_url'] = $upload['url'];
                $data['avatar_public_id'] = $upload['public_id'];
            } catch (RuntimeException|Throwable $exception) {
                report($exception);

                return back()
                    ->withInput($request->except('avatar'))
                    ->withErrors(['avatar' => 'Could not upload the photo. Please try again with a JPG or PNG under 5MB.']);
            }
        }

        $user->update($data);

        return redirect()->route('waiter.profile.edit')->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()->route('waiter.profile.edit')->with('status', 'Password updated successfully.');
    }
}
