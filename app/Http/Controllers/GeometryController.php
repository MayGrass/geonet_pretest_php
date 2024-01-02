<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Geometry;

class GeometryController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/geometry",
     *    tags={"Geometry"},
     *    summary="Store a new geometry",
     *    operationId="storeGeometry",
     *    @OA\RequestBody(
     *      required=true,
     *      description="Pass geometry data",
     *      @OA\JsonContent(
     *        required={"title","type","points"},
     *        @OA\Property(property="title", type="string", example="My Geometry"),
     *        @OA\Property(property="type", type="string", example="Point"),
     *        @OA\Property(property="points", type="string", example="1,1"),
     *      )
     *    )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "type" =>
                "required|in:Point,LineString,Polygon,MultiPoint,MultiLineString,MultiPolygon",
            "points" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $geometry = Geometry::create($validator->validated());

        return response()->json($geometry, 201);
    }
}
