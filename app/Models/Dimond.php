<?php

namespace App\Models;

use App\Models\Party;
use App\Models\Process;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dimond extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function parties()
    {
        return $this->belongsTo(Party::class);
    }

    public function processes()
    {
        return $this->hasMany(Process::class, 'dimonds_id');
    }
}
