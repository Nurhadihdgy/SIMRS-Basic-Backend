<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimrsSchedule extends Model
{
    protected $table = 'simrs_schedules';
    protected $primaryKey = 'schedule_id';
    public $timestamps = true;

    protected $fillable = [
        'doctor_id',
        'pol_id',
        'schedule_date',
        'schedule_start',
        'schedule_end',
        'created_at',
        'updated_at',
    ];

    public function doctor()
    {
        return $this->belongsTo(SimrsDoctor::class, 'doctor_id', 'doctor_id');
    }

    public function poliklinik()
    {
        return $this->belongsTo(SimrsPoliklinik::class, 'pol_id', 'pol_id');
    }
}
