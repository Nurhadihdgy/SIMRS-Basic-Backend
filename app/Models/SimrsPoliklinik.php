<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimrsPoliklinik extends Model
{
    protected $table = 'simrs_poliklinik';
    protected $primaryKey = 'pol_id';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'pol_id',
        'pol_name',
        'pol_description',
        'created_at',
        'updated_at',
    ];

    public function schedules()
    {
        return $this->hasMany(SimrsSchedule::class, 'pol_id', 'pol_id');
    }
}
