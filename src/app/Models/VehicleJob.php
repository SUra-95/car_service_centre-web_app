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
        'service_id',
        'is_deleted',
    ];
}
