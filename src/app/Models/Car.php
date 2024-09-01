<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Car extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'registration_number',
        'model',
        'fuel_type',
        'transmission',
        'customer_id',
        'is_deleted',
    ];
}
