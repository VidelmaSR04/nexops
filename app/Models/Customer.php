<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'registration_date',
        'name',
        'reporter_name',
        'reporter_contact',
        'type',
        'email',
        'address',
        'area',
        'latitude',
        'longitude',
        'package_name',
        'monthly_bill',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
