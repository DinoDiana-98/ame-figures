<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'activo',
        'orden'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer'
    ];

    // Relación con productos
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scope para categorías activas
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    // Scope para ordenar
    public function scopeOrdered($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Obtener la ruta de la categoría
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
