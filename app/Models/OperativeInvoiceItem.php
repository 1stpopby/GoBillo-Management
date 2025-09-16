<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperativeInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'operative_invoice_id',
        'day_of_week',
        'work_date',
        'worked',
        'hours_worked',
        'description',
        'day_rate',
        'total_amount',
    ];

    protected $casts = [
        'work_date' => 'date',
        'worked' => 'boolean',
        'hours_worked' => 'decimal:2',
        'day_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Day constants
    const DAYS = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    // Relationships
    public function operativeInvoice()
    {
        return $this->belongsTo(OperativeInvoice::class);
    }

    // Accessors
    public function getDayDisplayAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }

    // Methods
    public function calculateTotal(): void
    {
        if ($this->worked) {
            $this->total_amount = $this->hours_worked * ($this->day_rate / 8); // Assuming 8-hour day rate
        } else {
            $this->total_amount = 0;
        }
        $this->save();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if ($item->worked) {
                $item->total_amount = $item->hours_worked * ($item->day_rate / 8);
            } else {
                $item->total_amount = 0;
                $item->hours_worked = 0;
                $item->description = null;
            }
        });
    }
}