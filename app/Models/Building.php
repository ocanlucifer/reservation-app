<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active', // flag aktif
        'create_by',
        'update_by',
    ];

    // Relasi
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function schedules()
    {
        return $this->hasMany(BuildingSchedule::class, 'building_id');
    }

    // Query scope untuk filter gedung aktif
    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }
}

