<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Expenses extends Model
{
    use HasFactory;

    protected $table = 'expenses';
    protected $primaryKey = 'Expense_ID';
    public $incrementing = true; 
    protected $keyType = 'int'; 
    protected $fillable = [
        'Expense_ID',
        'Admin_ID',
        'Amount',
        'Desc_reason',
        'Receipt_filenameimg',
        'Datetime_taken'
    ];
    public function admin() {
        return $this->belongsTo(Admin::class, 'Admin_ID', 'Admin_ID');
    }
}
