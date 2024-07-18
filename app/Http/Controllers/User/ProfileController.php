<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function show() 
    {
        $client = Auth::user();

        return $this->sendResponse($client, 'Client Profile Viewed Succesfully');
    }

    public function update(Request $request)
    {
        $client = Auth::user();

        $formData = $request->validate([
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:users,email,'.$client->id],
            'birthday' => ['required', 'date'],
            'mobile_number' => ['required', 'string'],
            'password' => ['confirmed', Rules\Password::defaults()],
        ]);

        $client->update($formData);

        return $this->sendResponse($client, 'Client Profile Updated Successfully');
    }
}
