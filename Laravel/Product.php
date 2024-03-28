<?php


use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'producer_id', 'code', 'ean', 'tecdoc', 'description', 'weight'
    ];

    public $timestamps = false;

    public function producer() {
        return $this->belongsTo(\App\Models\Producer::class, 'producer_id', 'id');
    }

}
