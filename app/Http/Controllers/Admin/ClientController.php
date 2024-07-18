<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CreatedClient;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ClientController extends Controller
{
    public function store(Request $request)
    {
        $formData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:'.User::class],
            'projects' => ['required', 'array'],
            'birthday' => ['required', 'date'],
            'mobile_number' => ['required', 'string'],
        ]);
        $formData['password'] = 'password';
        $client = User::create($formData);
        $client->client_number = $client->id;
        $client->save();

        Mail::to($formData['email'])->send(new CreatedClient($client));
        
        return $this->sendResponse($client, 'Client Created Successfully', 201);
    }

    public function index()
    {
        $clients = QueryBuilder::for(User::class)
            ->allowedFields(['id', 'email', 'status', 'first_name', 'middle_name', 'last_name', 'projects'])
            ->allowedFilters([ 
                'projects',
                AllowedFilter::exact('status'),
                AllowedFilter::scope('created_at'),
                AllowedFilter::scope('search')])
                ->defaultSort('-created_at')
            ->paginate(request('per_page',10));

        return $this->sendResponse($clients, 'Clients Viewed Successfully');
    }

    public function tickets($userId)
    {
        $query = Ticket::where('ticketable_id', $userId)->where('ticketable_type', User::class)->with('ticketable');

        $tickets = QueryBuilder::for($query)
            ->allowedFilters([
            AllowedFilter::exact('status'),
            AllowedFilter::exact('project'),
            AllowedFilter::exact('type'),
            AllowedFilter::scope('client_search'),
            AllowedFilter::scope('starts_between')])
            ->defaultSort('-created_at')
            ->paginate(request('per_page',10));

        return $this->sendResponse($tickets, 'Client Tickets Viewed Successfully');
    }

    public function show(User $userId)
    {
        $userId->tickets;
        
        $data = ['client' => $userId];

        return $this->sendResponse($data, 'Client Viewed Successfully');
    }

    public function update(Request $request, User $userId)
    {
        $formData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255', 'unique:users,email,'.$userId->id],
            'birthday' => ['required', 'date'],
            'mobile_number' => ['required', 'string'],
            'projects' => ['required', 'array'],
            'password' => ['confirmed', Rules\Password::defaults()],
            // 'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $rawPassword = $request->has('password') ? $request->password : null;

        $userId->update($formData);

        if ($rawPassword) {
            Mail::to($formData['email'])->send(new CreatedClient($userId, $rawPassword));
        } 

        return $this->sendResponse($userId, 'Client Updated Successfully');
    }

    public function updateStatus(Request $request, User $userId)
    {
        $formData = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $userId->update($formData);

        return $this->sendResponse($userId, 'Client Status Updated Successfully');
    }
}
