<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    // protected $table = 'admins';
    protected $fillable = [
        'admin_lname',
        'admin_fname',
        'admin_mname',
        'admin_image',
        'birthdate',
        'phone_no',
        'address',
        'role',
        'email',
        'password',
    ];
}
