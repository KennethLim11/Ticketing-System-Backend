<?php
namespace App\Traits;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

trait AuthenticatesUsers
{
    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return $this->sendResponse(null, 'Logged out Successfully', 201);
    }
}