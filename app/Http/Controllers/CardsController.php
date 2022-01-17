<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use App\Models\Contain;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    public function create(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'name' => 'required|unique:cards|string',
            'description' => 'required|string',
            'collection_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $collection = Collection::where('name', $data->collection_name)->first();
                if($collection) {

                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    $card->save();

                    $contain = new Contain();
                    $contain->card_id = $card->id;
                    $contain->collection_id = $collection->id;
                    $contain->save();

                    $response['msg'] = "Carta creada correctamente con el id ".$card->id;
                } else {
                    $response['msg'] = "No existe ninguna coleccion con ese nombre";
                    $response['status'] = 0;
                }
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function addToCollection(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'card_name' => 'required|string',
            'collection_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $card = Card::where('name', $data->card_name)->first();
                if($card) {
                    $collection = Collection::where('name', $data->collection_name)->first();
                    if($collection) {

                        $contain = new Contain();
                        $contain->card_id = $card->id;
                        $contain->collection_id = $collection->id;
                        $contain->save();

                        $response['msg'] = "Carta añadida a la coleccion correctamente";
                    } else {
                        $response['msg'] = "No existe ninguna coleccion con ese nombre";
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "No existe ninguna carta con ese nombre";
                    $response['status'] = 0;
                }
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }
}
