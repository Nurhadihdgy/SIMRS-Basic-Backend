<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimrsDoctor extends Model
{
    protected $table = 'simrs_doctors';
    protected $primaryKey = 'doctor_id';
    public $incrementing = false; // karena primary key string
    public $timestamps = true;

    protected $fillable = [
        'doctor_id',
        'doctor_name',
        'doctor_gender',
        'doctor_phone_number',
        'doctor_address',
        'doctor_email',
        'doctor_bio',
        'created_at',
        'updated_at',
    ];

    public function schedules()
    {
        return $this->hasMany(SimrsSchedule::class, 'doctor_id', 'doctor_id');
    }
}
