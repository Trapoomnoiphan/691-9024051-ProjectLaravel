<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairStatusLog extends Model
{
    protected $table = 'repair_status_logs';

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'status',
        'note',
        'cost',
        'created_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function repairTicket()
    {
        return $this->belongsTo(RepairTicket::class, 'ticket_id');
    }
}
