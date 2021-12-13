<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
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
        'user_id',
        'user_type',
        'balance',
    ];

    public function deposit(float $value)
    {
        $this->update([
            'balance' => $this->attributes['balance'] + $value
        ]);
    }

    public function withdraw(float $value)
    {
        $this->update([
            'balance' => $this->attributes['balance'] - $value
        ]);
    }

    public function account()
    {
        return $this->morphTo();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user', 'id');
    }
}
