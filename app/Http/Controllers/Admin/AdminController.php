<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class AdminController extends Controller
{
    public function store(Request $request)
    {
        $formData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:'.Admin::class],
            'role' => ['required', 'string', 'in:super_admin,admin,staff'],
            'birthday' => ['required', 'date'],
            'mobile_number' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = Admin::create($formData);
        $admin->admin_number = $admin->id;
        $admin->save();
   
        return $this->sendResponse($admin, 'Admin Created Successfully', 201);
    }

    public function index()
    {
        $admins = QueryBuilder::for(Admin::class)
            ->allowedFields(['id', 'email', 'role', 'status', 'first_name', 'middle_name', 'last_name'])
            ->allowedFilters([ 
                AllowedFilter::exact('role'),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('created_at'),
                AllowedFilter::scope('search')])
                ->defaultSort('-created_at')
            ->paginate(request('per_page',10));

        return $this->sendResponse($admins, 'Admins Viewed Successfully');
    }

    public function show(Admin $adminId)
    {
        $data = ['admin' => $adminId];

        return $this->sendResponse($data, 'Admin Viewed Successfully');
    }

    public function update(Request $request, Admin $adminId)
    {
        $formData = $request->validate([
            // 'first_name' => ['required', 'string', 'max:255'],
            // 'last_name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:users,email,'.$adminId->id],
            // 'birthday' => ['required', 'date'],
            // 'mobile_number' => ['required', 'string'],
            'role' => ['required', 'string', 'in:super_admin,admin,staff'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $adminId->update($formData);

        return $this->sendResponse($adminId, 'Admin Updated Successfully');
    }

    public function updateStatus(Request $request, Admin $adminId)
    {
        $formData = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $adminId->update($formData);

        return $this->sendResponse($adminId, 'Admin Status Updated Successfully');
    }
}
