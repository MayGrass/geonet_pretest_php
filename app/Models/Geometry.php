<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use MatanYadaev\EloquentSpatial\Objects\Geometry as GeometryObject;
use App\Enums\GeoJsonTypeChoice;

class Geometry extends Model
{
    use HasSpatial;

    protected $table = "geometry";
    protected $fillable = ["type", "title", "geom"];

    protected $enumCasts = [
        "type" => GeoJsonTypeChoice::class,
    ];

    protected $casts = [
        "geom" => GeometryObject::class,
    ];
}
