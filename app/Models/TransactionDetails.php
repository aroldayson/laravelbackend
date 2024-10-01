<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transactions;
use App\Models\Laundrycategorys;

class TransactionDetails extends Model
{
    use HasFactory;
    protected $table = 'transaction_details';
    protected $primaryKey = 'TransacDet_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        "TransacDet_ID",
        "Categ_ID",
        'Transac_ID',
        'Qty',
        'Weight',
        'Price'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'Transac_ID', 'Transac_ID');
        return $this->hasMany(Laundrycategorys::class, 'Categ_ID', 'Categ_ID');
    }

    // public function category()
    // {
    //     return $this->belongsTo(Laundrycategorys::class, 'Categ_ID', 'Categ_ID');
    // }
}
