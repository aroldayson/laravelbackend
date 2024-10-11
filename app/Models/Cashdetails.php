<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Cashdetails extends Model
{
    use HasFactory;
    protected $table = 'cashed';
    protected $primaryKey = 'Cash_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        'Admin_ID',
        'Initial_amount',
        'Staff_ID',
        'Remitance',
        'Datetime_Changefund',
        'Datetime_Remitance',
    ];
    public function admin()
    {
        return $this->belongTo(Admin::class, 'Admin_ID', 'Admin_ID');
    }
}
