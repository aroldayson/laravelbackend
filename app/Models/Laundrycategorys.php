<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactioDetails;

class Laundrycategorys extends Model
{
    use HasFactory;
    protected $table = 'laundry_categorys';
    protected $primaryKey = 'Categ_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        'Categ_ID',
        'Category',
        'Per_kilograms',
    ];

    public function transactionDetails()
    {
        return $this->belongsTo(TransactionDetails::class, 'Categ_ID', 'Categ_ID');
    }
}
