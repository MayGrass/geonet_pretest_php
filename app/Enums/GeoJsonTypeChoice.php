<?php

namespace App\Enums;

class GeoJsonTypeChoice
{
    public const POINT = "Point";
    public const LINESTRING = "LineString";
    public const POLYGON = "Polygon";
    public const MULTIPOINT = "MultiPoint";
    public const MULTILINESTRING = "MultiLineString";
    public const MULTIPOLYGON = "MultiPolygon";

    public static function values(): array
    {
        return [
            self::POINT,
            self::LINESTRING,
            self::POLYGON,
            self::MULTIPOINT,
            self::MULTILINESTRING,
            self::MULTIPOLYGON,
        ];
    }
}
