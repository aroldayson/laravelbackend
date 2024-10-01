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
    protected $primaryKey = 'Transac_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        "Transac_ID",
        "Cust_ID",
        'Admin_ID',
        'Transac_date',
        'Transac_status',
        'Tracking_number',
        'Pickup_datetime',
        'Delivery_datetime'
    ];

    public function customers()
    {
        return $this->belongsTo(Customers::class, 'Cust_ID', 'Cust_ID');
        return $this->hasMany(TransactionDetails::class, 'Transac_ID', 'Transac_ID');
        return $this->hasMany(Payments::class, 'Transac_ID', 'Transac_ID');
        return $this->hasMany(Admin::class, 'Admin_ID', 'Admin_ID');
    }

    // public function admin()
    // {
    //     return $this->belongsTo(Admin::class, 'Admin_ID', 'Admin_ID');
    // }
}
