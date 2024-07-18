<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Guest;
use App\Mail\MailNotify;
use App\Traits\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email:rfs,dns', 'max:255'],
            'password' => 'required'
        ]);
        
        $client = User::where('email', $credentials['email'])->first();
        if ($client && Hash::check($credentials['password'], $client->password)) {
            $token = $client->createToken('myapptoken', ['user'])->plainTextToken;
            return $this->sendResponse([
                'token' => $token,
                'status' => $client->status
            ], 'Logged in Successfully', 201);
        }

        return $this->sendResponse(null, 'Invalid credentials', 401);  
    }

    public function store(Request $request)
    {
        $formData = $request->validate([
            'type' => 'required|in:System Issue,User-related Issue,Others',
            'type_other' => 'required_if:type,Others',
            'description' => 'required|string|max:256',
            'email' => 'required|email:rfs,dns|unique:users,email',
            'first_name' => 'required|string|max:256',
            'last_name' => 'required|string|max:256',
            'file' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:25600',
        ]);
        $guest = Guest::create($formData);

        $formData['reported_date'] = now();
        $formData['ticketable_id'] = $guest->id;
        $formData['ticketable_type'] = Guest::class;

        $ticket = Ticket::create($formData);

        $ticket->ticket_number = $ticket->id;

        if ($request->hasFile('file')) {
            $file = file_get_contents($request->file('file'));
            $ext = $request->file('file')->getClientOriginalExtension();
            $fileId = uniqid();
            $fileName = "{$fileId}_file.{$ext}";
            $year = date('Y');
            $month = date('m');

            if ($ticket->ticketable_id) {
                $filePath = "tickets/{$year}/{$month}/{$ticket->ticketable_id}/{$fileName}";
            } else {
                $filePath = "tickets/{$year}/{$month}/guest/{$fileName}";
            }

            // Store the file on the specified disk
            $disk = env('FILESYSTEM_DISK', 'local'); // Default to 'local' if FILESYSTEM_DISK is not set
            Storage::disk($disk)->put($filePath, $file);

            // Update the ticket's file_path_url with relative path
            $ticket->file_path_url = $filePath;
        }

        $ticket->save();

        // Send confirmation email to user after submitting a ticket
        Mail::to($formData['email'])->send(new MailNotify($ticket));

        return $this->sendResponse($ticket, 'Guest Ticket Stored Successfully');
    }
}
