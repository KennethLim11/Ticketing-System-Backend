<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Traits\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function register(Request $request)
    {
        $formData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:'.Admin::class],
            'role' => ['required', 'string', 'in:super_admin,admin,staff'],
            'birthday' => ['required', 'date'],
            'mobile_number' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $admin = Admin::create($formData);
        
        $token = $admin->createToken('myapptoken', ['super_admin'])->plainTextToken;

        $data = [
            'admin' => $admin,
            'token' => $token
        ];
        
        return $this->sendResponse($data, 'Admin Created and Logged in Successfully', 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255'],
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            $abilities = [];
            switch ($admin->role) {
                case 'super_admin':
                    $abilities = ['super_admin'];
                    break;
                case 'admin':
                    $abilities = ['admin'];
                    break;
                case 'staff':
                    $abilities = ['staff'];
                    break;
            }

            $token = $admin->createToken('myapptoken', $abilities)->plainTextToken;
            return $this->sendResponse([
                'token' => $token,
                'status' => $admin->status,
                'role' => $admin->role
            ], 'Logged in Successfully', 201);
        }

        return $this->sendResponse(null, 'Invalid credentials', 401);  
    }
}
