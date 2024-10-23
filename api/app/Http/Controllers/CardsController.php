<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Exception;
use Illuminate\Support\Facades\Validator;

class CardsController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Card",
     *     type="object",
     *     required={"id", "name", "description", "stroke", "defense"},
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="Card Name"),
     *     @OA\Property(property="description", type="string", example="Card Description"),
     *     @OA\Property(property="stroke", type="integer", example=10),
     *     @OA\Property(property="defense", type="integer", example=5)
     * )
     */

/**
 * @OA\Info(title="Tu API", version="1.0.0")
 * @OA\SecurityScheme(
 *     securityScheme="Bearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

/**
 * @OA\Get(
 *     path="/api/cards",
 *     summary="Obtener lista de cartas",
 *     tags={"Cartas"},
 *     security={{"Bearer": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cartas obtenidas exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="statusCode", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ocurrió un error al obtener las cartas"
 *     )
 * )
 */
public function index()
{
    $response = [
        'status' => 'success',
        'message' => 'Cards fetched successfully',
        'cards' => [],
        'statusCode' => 200
    ];
    try {
        $cards = Card::select('id', 'name', 'description', 'stroke', 'defense')->get()->toArray();
        $response['cards'] = $cards;
    } catch (Exception $e) {
        $response = [
            'statusCode' => 400,
            'status' => 'error',
            'message' => 'An error occurred while fetching the cards'
        ];
    }
    return response()->json($response, $response['statusCode']);
}





    /**
     * @OA\Post(
     *     path="/api/cards",
     *     summary="Crear una nueva carta",
     *     tags={"Cartas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="stroke", type="integer"),
     *             @OA\Property(property="defense", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Carta creada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación u otro problema"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $response = [
            'status' => 'success',
            'message' => 'Card created successfully',
            'statusCode' => 201
        ];
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'stroke' => 'required',
            'defense' => 'required'
        ];
        try {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response = [
                    'status' => 'error',
                    'message' => $validator->errors(),
                    'statusCode' => 400
                ];
            } else {
                $card = new Card();
                $card->name = $request->name;
                $card->description = $request->description;
                $card->stroke = $request->stroke;
                $card->defense = $request->defense;
                $card->save();
            }
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'An error occurred while creating the card',
                'statusCode' => 400
            ];
        }
        return response()->json($response, $response['statusCode']);
    }

    /**
     * @OA\Get(
     *     path="/api/cards/{id}",
     *     summary="Obtener una carta por ID",
     *     tags={"Cartas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carta obtenida exitosamente",
     *            ),
     *     @OA\Response(
     *         response=404,
     *         description="Carta no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $response = [
             'status' => 'success',
             'message' => 'Card fetched successfully',
             'card' => [],
             'statusCode' => 200
         ];
        try {
            $card = Card::select('id', 'name', 'description', 'stroke', 'defense')->find($id);
            if ($card) {
                $response['card'] = $card;
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Card not found',
                    'statusCode' => 404
                ];
            }
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'An error occurred while fetching the card',
                'statusCode' => 400
            ];
        }
        return response()->json($response, $response['statusCode']);
    }

    /**
     * @OA\Put(
     *     path="/api/cards/{id}",
     *     summary="Actualizar una carta",
     *     tags={"Cartas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="stroke", type="integer"),
     *             @OA\Property(property="defense", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carta actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al actualizar la carta"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $response = [
             'status' => 'success',
             'message' => 'Card updated successfully',
             'statusCode' => 200
         ];
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'stroke' => 'required',
            'defense' => 'required'
        ];
        try {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response = [
                    'status' => 'error',
                    'message' => $validator->errors(),
                    'statusCode' => 400
                ];
            } else {
                $card = Card::find($id);
                if ($card) {
                    $card->name = $request->name;
                    $card->description = $request->description;
                    $card->stroke = $request->stroke;
                    $card->defense = $request->defense;
                    $card->save();
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Card not found',
                        'statusCode' => 404
                    ];
                }
            }
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'An error occurred while updating the card',
                'statusCode' => 400
            ];
        }
        return response()->json($response, $response['statusCode']);
    }


}
