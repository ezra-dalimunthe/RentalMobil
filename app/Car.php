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
    protected $fillable = ['name', 'manufacture_id', 'license_number',
        'color', 'year', 'status', 'price', 'penalty'];
    public $incrementing = false;

    public static function boot()
    {

        parent::boot();
        static::deleted(function ($model) {
            $model->load("images");
            $images = $model->images;
            foreach ($images as $image) {
                if ($model->isForceDeleting()) {
                    //remove image file on disk;
                    $image->forceDelete();
                } else {
                    $image->delete();
                }
            }
        });
        static::restored(function ($model) {
            //WARNING: NOT TESTED YET.
            $model->load("images")->onlyTrashed();
            $images = $model->images;
            foreach ($images as $image) {
                $image->restore();
            }
        });
    }

    public function manufacture()
    {
        return $this->belongsTo('App\Manufacture');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, "car_id", "id");
    }
    public function images()
    {
        return $this->hasMany(CarImage::class);
    }

}
