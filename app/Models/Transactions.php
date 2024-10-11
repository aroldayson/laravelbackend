<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionDetails;
use App\Models\Customers;
use App\Models\Admin;
use App\Models\Payments;

class Transactions extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $primaryKey = 'Tracking_number';
    public $incrementing = true; 
    protected $keyType = 'string'; 
    protected $fillable = [
        "Tracking_number",
        "Cust_ID",
        'Admin_ID',
        'Tracking_number',
        'Transac_status',
        'Tracking_number',
        'Pickup_datetime',
        'Delivery_datetime'
    ];

    public function customers()
    {
        return $this->belongsTo(Customers::class, 'Cust_ID', 'Cust_ID');
        return $this->hasMany(TransactionDetails::class, 'Tracking_number', 'Tracking_number');
        return $this->hasMany(Payments::class, 'Tracking_number', 'Tracking_number');
        return $this->hasMany(Admin::class, 'Admin_ID', 'Admin_ID');
    }

    // public function admin()
    // {
    //     return $this->belongsTo(Admin::class, 'Admin_ID', 'Admin_ID');
    // }
}
