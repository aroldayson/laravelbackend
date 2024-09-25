<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $primaryKey = 'Cust_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        "Cust_ID",
        'Cust_lname',
        'Cust_fname',
        'Cust_mname',
        'Cust_phoneno',
        'Cust_address',
        'Cust_email',
        'Cust_password',
        'Cust_image'
    ];

    
}
