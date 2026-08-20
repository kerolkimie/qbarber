<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupPackage extends Model
{
    protected $fillable = ['points', 'price', 'status'];

    public function pointBatches()
    {
        return $this->hasMany(PointBatch::class);
    }
}
