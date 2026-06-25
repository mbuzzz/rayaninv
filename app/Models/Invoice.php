<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_name', 'date', 
        'subtotal', 'tax_rate', 'tax', 'total', 'status'
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
