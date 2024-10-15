<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Exception;
use Illuminate\Support\Facades\Validator;

class CardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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

            $cards = Card::select('id', 'name', 'description', 'stroke', 'defense')->get();
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
