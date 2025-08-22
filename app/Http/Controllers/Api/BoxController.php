<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateBoxRequest;
use App\Http\Resources\BoxResource;
use App\Models\Box;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * @OA\Info(title="Safeway Boxes API", version="1.0")
 */
class BoxController extends Controller
{
    /**
     * Create a new Box from JSON payload, store it in DB and return it.
     *
     * @OA\Post(
     *     path="/api/generate-box",
     *     summary="Create a box (payload)",
     *     tags={"Boxes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Box creation payload",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"height","width","color"},
     *             @OA\Property(property="height", type="integer", example=150, description="Height in px or units"),
     *             @OA\Property(property="width", type="integer", example=200, description="Width in px or units"),
     *             @OA\Property(property="color", type="string", example="#A1B2C3", description="Hex code or color name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Box created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="height", type="integer", example=150),
     *                 @OA\Property(property="width", type="integer", example=200),
     *                 @OA\Property(property="color", type="string", example="#A1B2C3"),
     *                 @OA\Property(property="createdAt", type="string", example="2025-08-21 17:30:12"),
     *                 @OA\Property(property="updatedAt", type="string", example="2025-08-21 17:30:12")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     *
     * @param  GenerateBoxRequest  $request
     * @return JsonResponse
     */
    public function generate(GenerateBoxRequest $request): JsonResponse
    {
        try {
            // Get validated payload (array)
            $data = $request->validated();

            // If you want to allow optional generation of dimensions when not provided,
            // uncomment the following and adjust rules in GenerateBoxRequest accordingly:
            //
            // $min = $data['min'] ?? 50;
            // $max = $data['max'] ?? 500;
            // if (!isset($data['height'])) $data['height'] = random_int($min, $max);
            // if (!isset($data['width']))  $data['width']  = random_int($min, $max);
            // if (empty($data['color']))   $data['color']  = sprintf('#%06X', random_int(0, 0xFFFFFF));

            // Create the Box (ensure $fillable in Box model includes height,width,color)
            $box = Box::create($data);

            // Return resource with 201 status
            return (new BoxResource($box))
                ->response()
                ->setStatusCode(201);

        } catch (QueryException $e) {
            Log::error('GenerateBox - DB error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'bindings' => method_exists($e, 'getBindings') ? $e->getBindings() : null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database error while creating box.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database failure'
            ], 500);

        } catch (Exception $e) {
            Log::error('GenerateBox - General error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unexpected error while creating box.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
