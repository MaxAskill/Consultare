<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'timestamp',
    ];

    protected $appends = ['readable_timestamp'];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function getReadableTimestampAttribute()
    {
        return $this->timestamp ? $this->timestamp->format('Y-m-d H:i:s') : null;
    }
}
