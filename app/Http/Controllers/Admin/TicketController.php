<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = QueryBuilder::for(Ticket::class)
            ->with('ticketable')
            ->join('users', 'users.id', 'tickets.ticketable_id')
            ->select('tickets.*', 'users.first_name', 'users.last_name')
            ->allowedFilters([
            AllowedFilter::exact('status'),
            AllowedFilter::exact('project'),
            AllowedFilter::exact('type'),
            AllowedFilter::scope('starts_between'), 
            AllowedFilter::scope('admin_search')])
            ->defaultSort('-created_at')
            ->paginate(request('per_page',10));

        return $this->sendResponse($tickets, 'Admin Tickets Viewed Successfully');
    }

    public function store(Request $request)
    {
        $admin = Auth::user();

        $formData = $request->validate([
            'type' => 'required|in:System Issue,User-related Issue,Others',
            'type_other' => 'required_if:type,Others',
            'description' => 'required|string|max:256',
            'email' => 'required|email:rfs,dns|unique:admins,email,'.$admin->id,
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'file' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:25600',
            'project' => 'required|string',
        ]);

        $formData['reported_date'] = now();
        $formData['ticketable_id'] = $admin->id;
        $formData['ticketable_type'] = Admin::class;

        $ticket = Ticket::create($formData);

        $ticket->ticket_number = $ticket->id;

        $admin->email = $formData['email'];
        $admin->first_name = $formData['first_name'];
        $admin->last_name = $formData['last_name'];
        $admin->save();

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
                $filePath = "tickets/{$year}/{$month}/admin/{$fileName}";
            }

            // Store the file on the specified disk
            $disk = env('FILESYSTEM_DISK', 'local'); // Default to 'local' if FILESYSTEM_DISK is not set
            Storage::disk($disk)->put($filePath, $file);

            // Update the ticket's file_path_url with relative path
            $ticket->file_path_url = $filePath;
        }

        $ticket->save();

        return $this->sendResponse($ticket, 'Admin Ticket Stored Successfully');
    }

    public function show(Ticket $ticketId)
    {
        $ticketId->ticketable;

        $data = ['ticket' => $ticketId];

        return $this->sendResponse($data, 'Admin Ticket Viewed Successfully');
    }

    public function update(Request $request, Ticket $ticketId)
    {
        $admin = Auth::user();

        $formData = $request->validate([
            'status' => ['required', 'string', 'in:Open,On-going,Closed'],
        ]);

        $ticketLog = TicketLog::create([
            'ticket_id' => $ticketId->id,
            'admin_id' => $admin->id,
            'new_status' => $formData['status'],
            'date_updated' => now(),
        ]);

        $ticketId->update($formData);

        $data = ['ticket' => $ticketId, 'ticket_log' => $ticketLog];

        return $this->sendResponse($data, 'Ticket Status Updated Successfully');
    }

    public function dashboardStatistics()
    {
        $admin = Auth::user();

        $totalTicketsCount = Ticket::count();

        $openTicketsCount = Ticket::where('status', 'Open')->count();

        $onGoingTicketsCount = Ticket::where('status', 'On-going')->count();

        $closedTicketsCount = Ticket::where('status', 'Closed')->count();

        $systemIssueCount = Ticket::where('type', 'System Issue')->count();

        $userIssueCount = Ticket::where('type', 'User-related Issue')->count();

        $othersIssueCount = Ticket::where('type', 'Others')->count();

        $issueTypes = [
            'System Issue' => $systemIssueCount,
            'User Related Issue' => $userIssueCount,
            'Others' => $othersIssueCount,
        ];

        $mostCommonIssue = array_keys($issueTypes, max($issueTypes));

        $data = [
            'admin' => $admin,
            'total_tickets' => $totalTicketsCount,
            'open_tickets' => $openTicketsCount,
            'onGoing_tickets' => $onGoingTicketsCount,
            'closed_tickets' => $closedTicketsCount,
            'most_common_issue' => $mostCommonIssue[0],
        ];

        return $this->sendResponse($data, 'Dashboard Statistics Viewed Successfully');
    }

    public function download(Ticket $ticketId)
    {
        $filePath = $ticketId->file_path_url;
        $filePath = storage_path('app/public/' . $filePath);

        return response()->download($filePath);
    }
}
