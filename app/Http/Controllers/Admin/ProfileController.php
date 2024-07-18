<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function show() 
    {
        $admin = Auth::user();

        return $this->sendResponse($admin, 'Admin Profile Viewed Succesfully');
    }

    public function update(Request $request)
    {
        $admin = Auth::user();

        $formData = $request->validate([
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:admins,email,'.$admin->id],
            'birthday' => ['required', 'date'],
            'mobile_number' => ['required', 'string'],
            'password' => ['confirmed', Rules\Password::defaults()],
        ]);

        $admin->update($formData);

        return $this->sendResponse($admin, 'Admin Profile Updated Successfully');
    }
}
