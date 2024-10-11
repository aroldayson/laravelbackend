<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transactions;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'Payment_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        'Payment_ID',
        "Admin_ID",
        "Tracking_number",
        'Amount',
        'Mode_of_Payment'
    ];

    public function transaction()
    {
        return $this->hasMany(Transactions::class, 'Tracking_number', 'Tracking_number');
        // return $this->hasMany(Laundrycategorys::class, 'Categ_ID', 'Categ_ID');
    }
}
