<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_pedido',
        'session_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'department',
        'city',
        'district',
        'reference',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'notes',
        'whatsapp_message'
    ];

    /**
     * Relación con los items del pedido
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relación con el usuario (si existe)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para pedidos pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para pedidos completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}