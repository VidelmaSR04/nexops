<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'customer_name',
        'asset_id',
        'created_by',
        'assigned_tech_name',
        'title',
        'subtitle',
        'description',
        'location',
        'priority',
        'status',
        'sla_due_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'sla_due_at'  => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(Technician::class, 'ticket_assignments')
            ->withPivot(['assigned_by', 'assigned_at', 'unassigned_at', 'is_active'])
            ->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TicketMaterial::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }
}

