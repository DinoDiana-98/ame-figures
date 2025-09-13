<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'cantidad'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer'
    ];

    // Relación con usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación con producto
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Calcular subtotal
    public function getSubtotalAttribute()
    {
        return $this->product->precio * $this->cantidad;
    }

    // Scope para carritos por session_id
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Scope para carritos de usuario
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Incrementar cantidad
    public function incrementQuantity($amount = 1)
    {
        $this->cantidad += $amount;
        $this->save();
    }

    // Decrementar cantidad
    public function decrementQuantity($amount = 1)
    {
        $this->cantidad = max(0, $this->cantidad - $amount);
        if ($this->cantidad === 0) {
            $this->delete();
        } else {
            $this->save();
        }
    }
}