<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class Admin extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guarded = ['id'];

    public function scopeCreatedAt(Builder $query, $startDate, $endDate): Builder
    {
        $query->where('created_at', '>=', Carbon::parse($startDate));
        $query->where('created_at', '<=', Carbon::parse($endDate));
        return $query;
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = explode(' ', $search);

        return $query->where(function ($subQuery) use ($search) {
            foreach ($search as $value) {
                $subQuery->where('role', 'LIKE', "%$value%")
                    ->orWhere('status', 'LIKE', "%$value%")
                    ->orWhere('first_name', 'LIKE', "%$value%")
                    ->orWhere('middle_name', 'LIKE', "%$value%")
                    ->orWhere('last_name', 'LIKE', "%$value%")
                    ->orWhere('admin_number', 'LIKE', "%$value%");
            }
        });
    }

    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        $fullName = $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        $fullName = preg_replace('/\s+/', ' ', $fullName);
        $fullName = trim($fullName);
        return ucwords($fullName);
    }

    public function ticketLogs()
    {
        return $this->hasMany(TicketLog::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    protected function setAdminNumberAttribute($value) {
        $this->attributes['admin_number'] = "ID" . str_pad($value, 4, "0", STR_PAD_LEFT);
    }
}
