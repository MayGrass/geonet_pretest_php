<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\GeoJsonTypeChoice;

class Geometry extends Model
{
    use HasFactory;

    protected $table = "geometry";
    protected $fillable = ["type", "title", "geom"];
    protected $enumCasts = [
        "type" => GeoJsonTypeChoice::class,
    ];

    public function __toString()
    {
        return $this->title;
    }
}
