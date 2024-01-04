<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Tests\TestCase;
use App\Models\Geometry;

class GeometryTest extends TestCase
{
    use RefreshDatabase;

    protected $newGeometry;

    public function setUp(): void
    {
        parent::setUp();

        // migrate database
        Artisan::call("migrate");

        // create test data
        $this->newGeometry = Geometry::create([
            "title" => "Point test",
            "type" => "Point",
            "geom" => Point::fromWKT("POINT(1 1)"),
        ]);
    }

    public function testCreate()
    {
        $baseTestData = ["title" => "test"];
        $url = route("geometry.store");

        // Test Point
        $pointTestData = $baseTestData + ["type" => "Point", "points" => "1,1"];

        $response = $this->postJson($url, $pointTestData);
        $response->assertStatus(200);

        // Test LineString
        $lineStringTestData = $baseTestData + [
            "type" => "LineString",
            "points" => "1,1;2,2",
        ];
        $response = $this->postJson($url, $lineStringTestData);
        $response->assertStatus(200);

        // Test Polygon
        $polygonTestData = $baseTestData + [
            "type" => "Polygon",
            "points" => "0,0;0,1;1,1;0,0",
        ];
        $response = $this->postJson($url, $polygonTestData);
        $response->assertStatus(200);

        // Test MultiPoint
        $multiPointTestData = $baseTestData + [
            "type" => "MultiPoint",
            "points" => "1,1|2,2",
        ];
        $response = $this->postJson($url, $multiPointTestData);
        $response->assertStatus(200);

        // Test MultiLineString
        $multiLineStringTestData = $baseTestData + [
            "type" => "MultiLineString",
            "points" => "1,1;2,2|3,3;4,4",
        ];
        $response = $this->postJson($url, $multiLineStringTestData);
        $response->assertStatus(200);

        // Test MultiPolygon
        $multiPolygonTestData = $baseTestData + [
            "type" => "MultiPolygon",
            "points" => "0,0;0,1;1,1;0,0|0,0;0,1;1,1;0,0",
        ];
        $response = $this->postJson($url, $multiPolygonTestData);
        $response->assertStatus(200);
    }

    public function testRetrieve()
    {
        $url = route("geometry.show", ["geometry" => $this->newGeometry->id]);
        $response = $this->getJson($url);
        $response->assertStatus(200);

        $responseAnswer = [
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "properties" => ["Title" => "Point test"],
                    "geometry" => [
                        "type" => "Point",
                        "coordinates" => [1.0, 1.0],
                    ],
                ],
            ],
        ];
        $response->assertExactJson($responseAnswer);
    }

    public function testUpdate()
    {
        $url = route("geometry.update", ["geometry" => $this->newGeometry->id]);
        $updateTestData = ["title" => "update test"];
        $response = $this->putJson($url, $updateTestData);
        $response->assertStatus(200);

        $this->assertEquals(
            $updateTestData["title"],
            Geometry::find($this->newGeometry->id)->title
        );
    }

    public function testDestroy()
    {
        $url = route("geometry.destroy", [
            "geometry" => $this->newGeometry->id,
        ]);
        $response = $this->deleteJson($url);
        $response->assertStatus(200);

        $this->assertDatabaseMissing("geometry", [
            "id" => $this->newGeometry->id,
        ]);
    }
}
