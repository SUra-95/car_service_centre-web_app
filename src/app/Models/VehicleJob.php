<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class VehicleJob extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'status',
        'car_id',
        'is_deleted',
    ];

    public function cars()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withPivot('status');
    }

    public function serviceCount()
    {
        return $this->belongsToMany(Service::class)->count();
    }
}
