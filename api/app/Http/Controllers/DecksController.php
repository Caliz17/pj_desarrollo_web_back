<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deck;
use Exception;
use Illuminate\Support\Facades\Validator;

class DecksController extends Controller
{
    /**
     * Recuperar un mazo por ID de jugador y ID de mazo.
     *
     * Esta función obtiene un mazo asociado con un jugador específico y un ID de mazo.
     * Devuelve una respuesta JSON que contiene la información del mazo o un mensaje de error.
     *
     * @param int $playerId El ID del jugador.
     * @param int $deckId El ID del mazo.
     * @return \Illuminate\Http\JsonResponse La respuesta que contiene la información del mazo o un mensaje de error.
     *
     * Posibles estados de respuesta:
     * - 200: Mazo obtenido exitosamente.
     * - 404: Mazo no encontrado.
     * - 400: Ocurrió un error al obtener el mazo.
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
            $deck = Deck::where('user_id', $playerId)->where('id_deck_player', $deckId)->first();
            if ($deck) {
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
                'message' => 'An error occurred while fetching the deck'. $e->getMessage()
            ];
        }
        return response()->json($response, $response['statusCode']);
    }

    /**
     * Crea un nuevo mazo para un jugador específico.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del mazo.
     * @param int $playerId El ID del jugador para el cual se está creando el mazo.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON que indica el resultado de la operación.
     *
     * Reglas de validación:
     * - id_card_#: requerido
     * - id_deck_player: requerido
     * - user_id: requerido
     *
     * La función valida los datos de entrada y verifica si el jugador ya tiene el máximo de 4 mazos.
     * Si la validación falla, devuelve un error de validación.
     * Si el jugador ya tiene 4 mazos, devuelve un mensaje de error.
     * Si todo es correcto, guarda el nuevo mazo en la base de datos y devuelve una respuesta de éxito.
     *
     * En caso de una excepción, devuelve un mensaje de error.
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
     * Actualiza un mazo existente para un jugador específico.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del mazo.
     * @param int $playerId El ID del jugador propietario del mazo.
     * @param int $deckId El ID del mazo que se va a actualizar.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON que indica el resultado de la operación.
     *
     * Reglas de validación:
     * - id_card_#: requerido
     * - id_deck_player: requerido
     * - user_id: requerido
     *
     * La función valida los datos de entrada y verifica si el mazo existe.
     * Si la validación falla, devuelve un error de validación.
     * Si el mazo no se encuentra, devuelve un mensaje de error.
     * Si todo es correcto, actualiza el mazo en la base de datos y devuelve una respuesta de éxito.
     *
     * En caso de una excepción, devuelve un mensaje de error.
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
