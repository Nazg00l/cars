<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    protected $primaryKey = 'id';

    // Which columns should be mass assignment. 
    protected $fillable = ['name', 'founded', 'description', 'image_path', 'user_id'];

    // Which columns (attributes) to be hidden
    protected $hidden = ['updated_at'];

    // Which columns (attributes) to be shown
    protected $visible = ['name', 'founded', 'description', 'created_at'];

    // Defining a relation ship to the "CarModel"
    public function carModels()
    {
        return $this->hasMany(CarModel::class);
    }

    // Define has many through relationship 
    public function engines() 
    {
        return $this->hasManyThrough(
                Engine::class,      // The first argument is the model we wish to access
                CarModel::class,    // The second argument is the intermediate model, 
                                    // meaning the model we need in order to access the 
                                    // first argument.
                'car_id',           // Foreign key on CarModel table
                'model_id'          // Foreign key on Engine table
            );
    }

    // Define has one through relationship
    public function productionDate() 
    {
        return $this->hasOneThrough(
            CarProductionDate::class,
            CarModel::class,
            'car_id',
            'model_id'
        );
    }

    // Define Many to Many relationship between cars and products
    public function products() 
    {
        return $this->belongsToMany(Product::class);
    }
}
