<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $table = 'cars';
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'manufacture_id', 'license_number', 'color', 'year', 'status', 'price', 'penalty'];
    public $incrementing = false;


    public function manufacture()
    {
        return $this->belongsTo('App\Manufacture');
    }
    public function image()
    {
        return $this->hasMany(CarImage::class);
    }

}
