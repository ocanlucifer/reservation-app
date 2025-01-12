<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'building_schedule_id',
        'visitor_company',
        'visitor_address',
        'visitor_address',
        'visitor_contact',
        'visitor_person',
        'visitor_note',
        'is_available',
        'is_booked',
        'tour_guide_requested',
        'tour_guide_assign',
        'is_confirm',
        'booked_date',
        'tour_guide_req_date',
        'tour_guide_assign_date',
        'confirm_date',
        'humas_id',
        'visitor_id',
        'koordinator_id',
        'tour_guide_id',
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
        return 'VR-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Relasi
    public function schedule()
    {
        return $this->belongsTo(BuildingSchedule::class, 'building_schedule_id');
    }

    public function humas()
    {
        return $this->belongsTo(User::class, 'humas_id');
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function tourGuide()
    {
        return $this->belongsTo(TourGuide::class, 'tour_guide_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}

