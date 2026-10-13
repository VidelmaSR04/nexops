<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nik',
        'name',
        'position',
        'area',
        'phone',
        'email',
        'status',
        'joined_at',
        'exp_years',
        'rating',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'rating'    => 'decimal:1',
        'exp_years' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class)->where('is_active', true);
    }
}

