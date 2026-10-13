<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'inventory_item_id',
        'item_name',
        'warehouse_id',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}

