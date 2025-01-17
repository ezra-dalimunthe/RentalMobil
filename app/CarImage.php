<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarImage extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $table = 'car_images';
    protected $dates = ['deleted_at'];
    protected $fillable = ['car_id', 'image'];
    public $incrementing = false;
    public $appends = ["imageUrl"];
    const IMAGE_PATH = "/image/car/";

    public static function boot()
    {

        parent::boot();
        static::deleted(function ($model) {
            if ($model->isForceDeleting()) {
                //remove image file on disk;
                $file = public_path(\App\CarImage::IMAGE_PATH . $model->image);
                unlink($file);
            }
        });
    }
    public function car()
    {
        return $this->belongsTo('App\Car');
    }
    public function getImageUrlAttribute()
    {
        return url(self::IMAGE_PATH . $this->image);
    }
}
