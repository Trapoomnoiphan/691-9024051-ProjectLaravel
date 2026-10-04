<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairType extends Model
{
    protected $table = 'repair_types';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'base_price',
        'created_at',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function repairTickets()
    {
        return $this->hasMany(RepairTicket::class, 'repair_type_id');
    }
}
