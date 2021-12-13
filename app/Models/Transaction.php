<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'payer_account_id',
        'payee_account_id',
        'value',
    ];

    public function payerAccount()
    {
        return $this->belongsTo(Account::class, 'payer_account_id');
    }

    public function payeeAccount()
    {
        return $this->belongsTo(Account::class, 'payee_account_id');
    }
}
