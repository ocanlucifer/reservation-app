<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'building_id',
        'tanggal',
        'start_time',
        'end_time',
        'is_available',
        'is_booked',
        'is_internal',
        'booked_date',
        'humas_id',
        'create_by',
        'update_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->transaction_number = self::generateTransactionNumber();
        });
    }

    public static function generateTransactionNumber()
    {
        $lastRecord = self::latest('id')->first();
        $lastNumber = $lastRecord ? intval(substr($lastRecord->transaction_number, -6)) : 0;
        $newNumber = $lastNumber + 1;
        return 'BS-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Relasi
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function humas()
    {
        return $this->belongsTo(User::class, 'humas_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function visitReservation()
    {
        return $this->hasOne(visitReservation::class, 'building_schedule_id');
    }
}

