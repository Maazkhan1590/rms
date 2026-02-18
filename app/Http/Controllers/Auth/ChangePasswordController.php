<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\College;
use App\Models\Department;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{
    public function edit()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $colleges = College::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('auth.passwords.edit', compact('colleges', 'departments'));
    }

    public function update(UpdatePasswordRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();
        // Mark password as changed now
        $data['password_changed_at'] = now();

        $user->update($data);

        return redirect()->route('profile.password.edit')->with('message', __('global.change_password_success'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Handle credentials file upload
        if ($request->hasFile('credentials_file')) {
            // Delete old file if exists
            if ($user->credentials_file && Storage::disk('public')->exists($user->credentials_file)) {
                Storage::disk('public')->delete($user->credentials_file);
            }
            $data['credentials_file'] = $request->file('credentials_file')->store('credentials', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.password.edit')->with('message', __('global.update_profile_success'));
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->update([
            'email' => time() . '_' . $user->email,
        ]);

        $user->delete();

        return redirect()->route('login')->with('message', __('global.delete_account_success'));
    }
}
