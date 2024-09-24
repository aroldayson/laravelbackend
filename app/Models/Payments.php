<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $fillable = [
        'admin_id',
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
