<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Geometry;
use App\Enums\GeoJsonTypeChoice;
use App\Http\Services\GeometryProcessor;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailedResponse;

class GeometryController extends Controller
{
    /**
     * @OA\Get(
     *    path="/interview/api/geometry/{id}",
     *    tags={"Geometry"},
     *    summary="Get a geometry",
     *    operationId="getGeometries",
     *    @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     *    @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function show(string $id)
    {
        $geometry = Geometry::findOrFail($id);

        $geojson = [
            "type" => "FeatureCollection",
            "features" => [
                "type" => "Feature",
                "properties" => ["Title" => $geometry->title],
                "geometry" => json_decode($geometry->geom->toJson()),
            ],
        ];

        return response()->json($geojson);
    }

    /**
     * @OA\Post(
     *    path="/interview/api/geometry",
     *    tags={"Geometry"},
     *    summary="Store a new geometry",
     *    operationId="storeGeometry",
     *    @OA\RequestBody(
     *      description="Pass geometry data",
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *            @OA\Property(
     *               property="title",
     *               type="string",
     *               description="Title of the geometry",
     *               example="My Geometry"
     *            ),
     *            @OA\Property(
     *               property="type",
     *               type="choice",
     *               description="Type of the geometry",
     *               enum={"Point", "LineString", "Polygon", "MultiPoint", "MultiLineString", "MultiPolygon"},
     *               example="Point"
     *            ),
     *            @OA\Property(
     *               property="points",
     *               type="string",
     *               description="Points data",
     *               example="1,1"
     *            ),
     *         )
     *      )
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "type" =>
                "required|in:" . implode(",", GeoJsonTypeChoice::values()),
            "points" => "required|string",
        ]);

        if ($validator->fails()) {
            return new FailedResponse(message: $validator->errors());
        }

        $validated_data = $validator->validated();

        $geomProcessor = new GeometryProcessor($validated_data["points"]);

        try {
            switch ($validated_data["type"]) {
                case GeoJsonTypeChoice::POINT:
                    $validated_data["geom"] = $geomProcessor->getPoint();
                    break;
                case GeoJsonTypeChoice::LINESTRING:
                    $validated_data["geom"] = $geomProcessor->getLineString();
                    break;
                case GeoJsonTypeChoice::POLYGON:
                    $validated_data["geom"] = $geomProcessor->getPolygon();
                    break;
                case GeoJsonTypeChoice::MULTIPOINT:
                    $validated_data["geom"] = $geomProcessor->getMultiPoint();
                    break;
                case GeoJsonTypeChoice::MULTILINESTRING:
                    $validated_data[
                        "geom"
                    ] = $geomProcessor->getMultiLineString();
                    break;
                case GeoJsonTypeChoice::MULTIPOLYGON:
                    $validated_data["geom"] = $geomProcessor->getMultiPolygon();
                    break;
            }
        } catch (Exception $e) {
            return new FailedResponse(message: $e->getMessage());
        }
        $geometry = Geometry::create($validated_data);

        return new SuccessResponse(["id" => $geometry->id]);
    }

    /**
     * @OA\Put(
     *   path="/interview/api/geometry/{id}",
     *   tags={"Geometry"},
     *   summary="Update a geometry",
     *   operationId="updateGeometry",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(in="query", name="title", required=true, @OA\Schema(type="string")),
     *   @OA\Response(
     *     response=200,
     *     description="OK"
     *   )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $geometry = Geometry::findOrFail($id);
        $geometry->update($validator->validated());

        return new SuccessResponse();
    }

    /**
     * @OA\Delete(
     *   path="/interview/api/geometry/{id}",
     *   tags={"Geometry"},
     *   summary="Delete a geometry",
     *   operationId="deleteGeometry",
     *   @OA\Parameter(in="path", name="id", required=true, @OA\Schema(type="string")),
     *   @OA\Response(
     *     response=204,
     *     description="No Content"
     *   )
     * )
     */
    public function destroy(string $id)
    {
        $geometry = Geometry::findOrFail($id);
        $geometry->delete();

        return new SuccessResponse();
    }
}
