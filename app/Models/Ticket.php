<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public function scopeStartsBetween(Builder $query, $startDate, $endDate): Builder
    {
        $query->where('reported_date', '>=', Carbon::parse($startDate));
        $query->where('reported_date', '<=', Carbon::parse($endDate));
        return $query;
    }

    public function scopeAdminSearch(Builder $query, string $search): Builder
    {
        $search = explode(' ', $search);

        return $query->where(function ($subQuery) use ($search) {
            foreach ($search as $value) {
                $subQuery->where('ticket_number', 'LIKE', "%$value%")
                    ->orWhere('tickets.status', 'LIKE', "%$value%")
                    ->orWhere('type', 'LIKE', "%$value%")
                    ->orWhere('project', 'LIKE', "%$value%")
                    ->orWhere('users.first_name', 'LIKE', "%$value%")
                    ->orWhere('users.middle_name', 'LIKE', "%$value%")
                    ->orWhere('users.last_name', 'LIKE', "%$value%");
            }
        });
    }

    public function scopeClientSearch(Builder $query, string $search): Builder
    {
        $search = explode(' ', $search);

        return $query->where(function ($subQuery) use ($search) {
            foreach ($search as $value) {
                $subQuery->where('ticket_number', 'LIKE', "%$value%")
                    ->orWhere('project', 'LIKE', "%$value%")
                    ->orWhere('status', 'LIKE', "%$value%")
                    ->orWhere('type', 'LIKE', "%$value%");
            }
        });
    }

    protected $casts = [
        'reported_date' => 'date',
    ];

    protected $appends = ['file_link'];

    public function ticketable()
    {
        return $this->morphTo();
    }

    protected function getFileLinkAttribute(): ?string
    {
        return $this->file_path_url ? $this->generateTemporaryUrl($this->file_path_url) : null;
    }

    protected function generateTemporaryUrl(string $path): string
    {
        return Storage::url($path, now()->addHour());
        // return env('APP_URL').Storage::url($path, now()->addHour());
        //return Storage::path("/public/$path");
    }

    protected function setTicketNumberAttribute($value) {
        $this->attributes['ticket_number'] = "TN" . str_pad($value, 5, "0", STR_PAD_LEFT);
    }
}
