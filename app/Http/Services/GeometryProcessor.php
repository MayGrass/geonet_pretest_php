<?php

namespace App\Http\Services;

use Exception;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\MultiPoint;
use MatanYadaev\EloquentSpatial\Objects\MultiLineString;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;

class GeometryProcessor
{
    /** @var string */
    private $points;

    public function __construct(string $points)
    {
        $this->points = $points;
    }

    protected function validate_error(string $message)
    {
        throw new Exception($message);
    }

    private function validateNumber(array $coords): array
    {
        try {
            return array_map(function ($coord) {
                return array_map("floatval", explode(",", $coord));
            }, $coords);
        } catch (Exception $e) {
            throw $this->validate_error("Invalid points format.");
        }
    }

    public function validatePoint(): array
    {
        $point = explode(",", $this->points);
        if (count($point) > 2) {
            throw $this->validate_error("Point type expects only 1 point.");
        }

        try {
            return array_map("floatval", $point);
        } catch (Exception $e) {
            throw $this->validate_error("Invalid points format.");
        }
    }

    public function validateLineString(): array
    {
        $points = explode(";", $this->points);
        if (count($points) < 2) {
            throw $this->validate_error(
                "LineString type expects at least 2 points."
            );
        }
        return $this->validateNumber($points);
    }

    public function validatePolygon(): array
    {
        $polygon = explode(";", $this->points);
        if (count($polygon) < 4 || $polygon[0] !== end($polygon)) {
            throw $this->validate_error(
                "Polygon type expects at least 4 points and the first and last points must be the same."
            );
        }
        return $this->validateNumber($polygon);
    }

    public function validateMultiPoint(): array
    {
        $points = explode("|", $this->points);
        if (count($points) < 2) {
            throw $this->validate_error(
                "MultiPoint type expects at least 2 points."
            );
        }

        $singlePoints = [];
        foreach ($points as $point) {
            $geomProcessor = new GeometryProcessor($point);
            $pointCoords = $geomProcessor->validatePoint();
            $singlePoints[] = new Point($pointCoords[0], $pointCoords[1]);
        }

        return $singlePoints;
    }

    public function validateMultiLineString(): array
    {
        $multiLineString = explode("|", $this->points);
        if (count($multiLineString) < 2) {
            throw $this->validate_error(
                "MultiLineString type expects at least 2 lines."
            );
        }

        $lines = [];
        foreach ($multiLineString as $line) {
            $geomProcessor = new GeometryProcessor($line);
            $lines[] = new LineString($geomProcessor->validateLineString());
        }

        return $lines;
    }

    public function validateMultiPolygon(): array
    {
        $multiPolygon = explode("|", $this->points);
        if (count($multiPolygon) < 2) {
            throw $this->validate_error(
                "MultiPolygon type expects at least 2 polygons."
            );
        }

        $polygons = [];
        foreach ($multiPolygon as $polygon) {
            $geomProcessor = new GeometryProcessor($polygon);
            $polygons[] = new Polygon($geomProcessor->validatePolygon());
        }

        return $polygons;
    }

    public function getPoint(): Point
    {
        $point = $this->validatePoint();
        return new Point($point[0], $point[1]);
    }

    public function getLineString(): LineString
    {
        $lineString = $this->validateLineString();
        return new LineString($lineString);
    }

    public function getPolygon(): Polygon
    {
        $polygon = $this->validatePolygon();
        return new Polygon($polygon);
    }

    public function getMultiPoint(): MultiPoint
    {
        $multiPoint = $this->validateMultiPoint();
        return new MultiPoint($multiPoint);
    }

    public function getMultiLineString(): MultiLineString
    {
        $multiLineString = $this->validateMultiLineString();
        return new MultiLineString($multiLineString);
    }

    public function getMultiPolygon(): MultiPolygon
    {
        $multiPolygon = $this->validateMultiPolygon();
        return new MultiPolygon($multiPolygon);
    }
}
