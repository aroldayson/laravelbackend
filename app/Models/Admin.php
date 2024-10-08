<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transactions;
use App\Models\Expenses;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'admins';
    protected $primaryKey = 'Admin_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        'Admin_ID',
        'Admin_lname',
        'Admin_fname',
        'Admin_mname',
        'Admin_image',
        'Birthdate',
        'Phone_no',
        'Address',
        'Role',
        'Email',
        'Password',
    ];
    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'Admin_ID', 'Admin_ID');
        return $this->hasMany(Expenses::class, 'Admin_ID', 'Admin_ID');
    }
}
