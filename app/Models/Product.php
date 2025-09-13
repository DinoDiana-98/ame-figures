<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\OrderItem; // ← Añadir esta importación

class Product extends Model
{
    protected $fillable = [
        'sku',
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'precio_descuento',
        'stock',
        'stock_minimo',
        'imagen1',
        'imagen2',
        'imagen3',
        'material',
        'size',
        'incluye',
        'activo',
        'destacado',
        'category_id'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_descuento' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'category_id' => 'integer'
    ];

    // Boot method para generar slug automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->nombre) . '-' . Str::random(5);
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->nombre) . '-' . Str::random(5);
            }
        });
    }

    // Relación con categoría
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con carrito
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // Relación con items de orden - CORREGIDO: añadir tipo de retorno
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope para productos activos
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    // Scope para productos destacados
    public function scopeFeatured($query)
    {
        return $query->where('destacado', true)->where('activo', true);
    }

    // Scope para productos con stock
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Scope para búsqueda
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
    }

    // Calcular precio con descuento
    public function getPrecioFinalAttribute()
    {
        return $this->precio_descuento ?: $this->precio;
    }

    // Verificar si tiene descuento
    public function getTieneDescuentoAttribute()
    {
        return !is_null($this->precio_descuento);
    }

    // Verificar stock bajo
    public function getStockBajoAttribute()
    {
        return $this->stock <= $this->stock_minimo;
    }

    // Obtener imagen principal
    public function getImagenPrincipalAttribute()
    {
        return $this->imagen1 ?: $this->imagen2 ?: $this->imagen3;
    }

    // Obtener todas las imágenes como array
    public function getImagenesAttribute()
    {
        return array_filter([
            $this->imagen1,
            $this->imagen2,
            $this->imagen3
        ]);
    }

    // Obtener la ruta del producto
    public function getRouteKeyName()
    {
        return 'slug';
    }
}