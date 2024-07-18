<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\MailNotify;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketController extends Controller
{
    public function index()
    {
        $client = Auth::user();

        $query = Ticket::where('ticketable_id', $client->id)->where('ticketable_type', User::class)->with('ticketable');

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

    public function show(Ticket $ticketId)
    {
        $ticketId->ticketable;

        $data = ["ticket" => $ticketId];

        return $this->sendResponse($data, 'Client Ticket Viewed Successfully');
    }

    public function store(Request $request)
    {
        $client = Auth::user();

        $formData = $request->validate([
            'type' => 'required|in:System Issue,User-related Issue,Others',
            'type_other' => 'required_if:type,Others',
            'description' => 'required|string|max:256',
            'email' => 'required|email:rfs,dns|unique:users,email,'.$client->id,
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'file' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:25600',
            'project' => 'required|string',
        ]);

        $formData['reported_date'] = now();
        $formData['ticketable_id'] = $client->id;
        $formData['ticketable_type'] = User::class;

        $ticket = Ticket::create($formData);

        $ticket->ticket_number = $ticket->id;

        $client->email = $formData['email'];
        $client->first_name = $formData['first_name'];
        $client->last_name = $formData['last_name'];
        $client->save();

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

        return $this->sendResponse($ticket, 'Client Ticket Stored Successfully');
    }

    public function dashboardStatistics()
    {
        $client = Auth::user();

        $totalTicketsCount = Ticket::where('ticketable_id', $client->id)
                                ->where('ticketable_type', User::class)
                                ->count();

        $openTicketsCount = Ticket::where('ticketable_id', $client->id)
                                ->where('ticketable_type', User::class)
                                ->where('status', 'Open')
                                ->count();

        $onGoingTicketsCount = Ticket::where('ticketable_id', $client->id)
                                    ->where('ticketable_type', User::class)
                                    ->where('status', 'On-going')
                                    ->count();

        $closedTicketsCount = Ticket::where('ticketable_id', $client->id)
                                    ->where('ticketable_type', User::class)
                                    ->where('status', 'Closed')
                                    ->count();

        $systemIssueCount = Ticket::where('ticketable_id', $client->id)
                                ->where('ticketable_type', User::class)
                                ->where('type', 'System Issue')
                                ->count();

        $userIssueCount = Ticket::where('ticketable_id', $client->id)
                                ->where('ticketable_type', User::class)
                                ->where('type', 'User-related Issue')
                                ->count();

        $othersIssueCount = Ticket::where('ticketable_id', $client->id)
                                ->where('ticketable_type', User::class)
                                ->where('type', 'Others')
                                ->count();

        $issueTypes = [
            'System Issue' => $systemIssueCount,
            'User Related Issue' => $userIssueCount,
            'Others' => $othersIssueCount,
        ];

        $mostCommonIssue = array_keys($issueTypes, max($issueTypes));

        $data = [
            'client' => $client,
            'total_tickets' => $totalTicketsCount,
            'open_tickets' => $openTicketsCount,
            'onGoing_tickets' => $onGoingTicketsCount,
            'closed_tickets' => $closedTicketsCount,
            'most_common_issue' => $mostCommonIssue[0],
        ];

        return $this->sendResponse($data, 'Dashboard Statistics Viewed Successfully');
    }

}
