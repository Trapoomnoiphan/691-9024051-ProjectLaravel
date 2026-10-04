<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairTicket extends Model
{
    protected $table = 'repair_tickets';

    public $timestamps = false;

    protected $fillable = [
        'tracking_code',
        'user_id',
        'repair_type_id',
        'device_model',
        'issue_description',
        'status',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function repairType()
    {
        return $this->belongsTo(RepairType::class, 'repair_type_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(RepairStatusLog::class, 'ticket_id')->orderBy('id', 'asc');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(RepairStatusLog::class, 'ticket_id')->latestOfMany('id');
    }

    public function getTotalCostAttribute()
    {
        $latestCost = $this->statusLogs()->whereNotNull('cost')->orderBy('id', 'desc')->value('cost');
        if ($latestCost !== null) {
            return $latestCost;
        }
        return $this->repairType ? $this->repairType->base_price : 0;
    }
}
