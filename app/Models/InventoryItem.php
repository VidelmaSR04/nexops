<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    public const CONDITIONS = ['Baru', 'Baik', 'Perlu Perbaikan', 'Rusak'];

    public const AVAILABILITIES = ['Tersedia', 'Sedang Digunakan', 'Sedang Diperbaiki', 'Hilang'];

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'unit',
        'min_stock',
        'status',
        'condition',
        'availability',
        'serial_example',
        'description',
    ];

    protected $casts = [
        'min_stock' => 'integer',
    ];

    public static function stockLevel(int $total, int $min, ?string $status = null): string
    {
        if ($status === 'Nonaktif') {
            return 'Aman';
        }

        if ($total <= 0) {
            return 'Habis';
        }

        if ($min > 0 && $total <= $min) {
            return 'Menipis';
        }

        return 'Aman';
    }

    public static function isWorseStockLevel(string $before, string $after): bool
    {
        return ($before === 'Aman' && in_array($after, ['Menipis', 'Habis'], true))
            || ($before === 'Menipis' && $after === 'Habis');
    }

    public function logWorsenedStockLevel(string $before, int $total, ?User $user): void
    {
        $level = self::stockLevel($total, (int) $this->min_stock, $this->status);
        if (! self::isWorseStockLevel($before, $level)) {
            return;
        }

        $action = $level === 'Habis' ? 'out_of_stock' : 'low_stock';
        $description = $level === 'Habis'
            ? "Stok {$this->name} habis: {$total} {$this->unit} (minimum {$this->min_stock})"
            : "Stok {$this->name} menipis: {$total} {$this->unit} (minimum {$this->min_stock})";

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'subject_type' => self::class,
            'subject_id' => $this->id,
            'action' => $action,
            'description' => $description,
            'properties' => [
                'previous_stock_level' => $before,
                'stock_level' => $level,
                'total_stock' => $total,
                'min_stock' => (int) $this->min_stock,
            ],
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_stocks')
            ->withPivot(['quantity', 'min_quantity'])
            ->withTimestamps();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function ticketMaterials(): HasMany
    {
        return $this->hasMany(TicketMaterial::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stocks()->sum('quantity');
    }
}
