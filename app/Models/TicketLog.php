<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $dates = ['date_updated'];

    /**
     * Get the ticket associated with the ticket log.
     */
    public function ticket()
    {
        return $this->morphMany(Ticket::class, 'ticketable');
    }

    /**
     * Get the admin associated with the ticket log.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Automatically set date_updated when saving
    public function setDateUpdatedAttribute($value)
    {
        $this->attributes['date_updated'] = $value ?: Carbon::now();
    }
}
