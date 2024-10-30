<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deck;
use Exception;
use Illuminate\Support\Facades\Validator;

class DecksController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/decks/{playerId}/{deckId}",
     *     summary="Recuperar un mazo por ID de jugador y ID de mazo",
     *     tags={"Mazos"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *         name="deckId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mazo obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Deck fetched successfully"),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mazo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Deck not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ocurrió un error al obtener el mazo",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred while fetching the deck")
     *         )
     *     )
     * )
     */
    public function getDeckByPlayer($playerId, $deckId)
    {
        $response = [
            'status' => 'success',
            'message' => 'Deck fetched successfully',
            'deck' => [],
            'statusCode' => 200
        ];
        try {
            $deck = Deck::where('user_id', $playerId)
                ->where('id_deck_player', $deckId)
                ->with(['card1', 'card2', 'card3', 'card4', 'card5', 'card6', 'card7', 'card8'])
                ->first();

            if ($deck) {
                $deck->makeHidden(['id_card_1', 'id_card_2', 'id_card_3', 'id_card_4', 'id_card_5', 'id_card_6', 'id_card_7', 'id_card_8']);
                $response['deck'] = $deck;
            } else {
                $response = [
                    'statusCode' => 404,
                    'status' => 'error',
                    'message' => 'Deck not found'
                ];
            }
        } catch (Exception $e) {
            $response = [
                'statusCode' => 400,
                'status' => 'error',
                'message' => 'An error occurred while fetching the deck: ' . $e->getMessage()
            ];
        }
        return response()->json($response, $response['statusCode']);
    }



    /**
     * @OA\Post(
     *     path="/api/decks/{playerId}",
     *     summary="Crea un nuevo mazo para un jugador específico",
     *     tags={"Mazos"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_card_1", type="integer", example=1),
     *             @OA\Property(property="id_card_2", type="integer", example=2),
     *             @OA\Property(property="id_card_3", type="integer", example=3),
     *             @OA\Property(property="id_card_4", type="integer", example=4),
     *             @OA\Property(property="id_card_5", type="integer", example=5),
     *             @OA\Property(property="id_card_6", type="integer", example=6),
     *             @OA\Property(property="id_card_7", type="integer", example=7),
     *             @OA\Property(property="id_card_8", type="integer", example=8),
     *             @OA\Property(property="id_deck_player", type="string", example="Deck1"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mazo creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Mazo creado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o al crear el mazo",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object", example={"id_card_1": "Este campo es obligatorio"})
     *         )
     *     )
     * )
     */
    public function createDeck(Request $request, $playerId)
    {
        $response = [
            'status' => 'success',
            'message' => 'Deck created successfully',
            'statusCode' => 201
        ];
        $rules = [
            'id_card_1' => 'required',
            'id_card_2' => 'required',
            'id_card_3' => 'required',
            'id_card_4' => 'required',
            'id_card_5' => 'required',
            'id_card_6' => 'required',
            'id_card_7' => 'required',
            'id_card_8' => 'required',
            'id_deck_player' => 'required',
            'user_id' => 'required'
        ];
        try {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response = [
                    'statusCode' => 400,
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ];
            } else {
                $existingDecksCount = Deck::where('user_id', $playerId)->count();

                // Limitar a un máximo de 4 mazos por jugador
                if ($existingDecksCount >= 4) {
                    $response = [
                                'statusCode' => 400,
                                'status' => 'error',
                                'message' => 'El jugador ya tiene el máximo de 4 mazos.'
                    ];
                }
                $deck = new Deck();
                $deck->id_card_1 = $request->id_card_1;
                $deck->id_card_2 = $request->id_card_2;
                $deck->id_card_3 = $request->id_card_3;
                $deck->id_card_4 = $request->id_card_4;
                $deck->id_card_5 = $request->id_card_5;
                $deck->id_card_6 = $request->id_card_6;
                $deck->id_card_7 = $request->id_card_7;
                $deck->id_card_8 = $request->id_card_8;
                $deck->id_deck_player = $request->id_deck_player;
                $deck->user_id = $request->user_id;
                $deck->save();
            }
        } catch (Exception $e) {
            $response = [
                'statusCode' => 400,
                'status' => 'error',
                'message' => 'An error occurred while creating the deck'
            ];
        }
        return response()->json($response, $response['statusCode']);

    }

    /**
     * @OA\Put(
     *     path="/api/decks/{playerId}/{deckId}",
     *     summary="Actualiza un mazo existente para un jugador específico",
     *     tags={"Mazos"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *         name="deckId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_card_1", type="integer", example=1),
     *             @OA\Property(property="id_card_2", type="integer", example=2),
     *             @OA\Property(property="id_card_3", type="integer", example=3),
     *             @OA\Property(property="id_card_4", type="integer", example=4),
     *             @OA\Property(property="id_card_5", type="integer", example=5),
     *             @OA\Property(property="id_card_6", type="integer", example=6),
     *             @OA\Property(property="id_card_7", type="integer", example=7),
     *             @OA\Property(property="id_card_8", type="integer", example=8),
     *             @OA\Property(property="id_deck_player", type="string", example="Deck1", description="Nombre del mazo"),
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID del usuario propietario del mazo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mazo actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Mazo actualizado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o al actualizar el mazo",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object", example={"id_card_1": "Este campo es obligatorio"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="El mazo no se encontró",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="El mazo con el ID especificado no se encontró.")
     *         )
     *     )
     * )
     */
    public function updateDeck(Request $request, $playerId, $deckId)
    {
        $response = [
            'status' => 'success',
            'message' => 'Deck updated successfully',
            'statusCode' => 200
        ];

        $rules = [
            'id_card_1' => 'required',
            'id_card_2' => 'required',
            'id_card_3' => 'required',
            'id_card_4' => 'required',
            'id_card_5' => 'required',
            'id_card_6' => 'required',
            'id_card_7' => 'required',
            'id_card_8' => 'required',
            'id_deck_player' => 'required',
            'user_id' => 'required'
        ];

        try {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response = [
                    'statusCode' => 400,
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ];
            } else {
                // Buscar el mazo existente
                $deck = Deck::where('user_id', $playerId)->where('id_deck_player', $deckId)->first();

                // Verificar si el mazo existe
                if (!$deck) {
                    return response()->json(['message' => 'El mazo no se encontró.'], 404);
                }

                // Actualizar los campos del mazo
                $deck->id_card_1 = $request->id_card_1;
                $deck->id_card_2 = $request->id_card_2;
                $deck->id_card_3 = $request->id_card_3;
                $deck->id_card_4 = $request->id_card_4;
                $deck->id_card_5 = $request->id_card_5;
                $deck->id_card_6 = $request->id_card_6;
                $deck->id_card_7 = $request->id_card_7;
                $deck->id_card_8 = $request->id_card_8;
                $deck->id_deck_player = $request->id_deck_player;
                $deck->user_id = $request->user_id;
                $deck->save();
            }
        } catch (Exception $e) {
            $response = [
                'statusCode' => 400,
                'status' => 'error',
                'message' => 'An error occurred while updating the deck'
            ];
        }

        return response()->json($response, $response['statusCode']);
    }

}
