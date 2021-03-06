<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use App\Models\Contain;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CollectionsController extends Controller
{
    public function create(Request $req) {
        $data = $req->getContent();

        if($data) {
            $validator = Validator::make(json_decode($data, true), [
                'name' => 'required|unique:collections|string',
                'symbol' => 'required|string',
                'release_date' => 'required|date_format:Y-m-d',
                'card_id' => 'integer',
                'card_name' => 'string',
                'card_description' => 'string',
            ], [
                'date_format' => 'El formato no coincide con YYYY-MM-DD (1999-03-25)',
            ]);

            $nessages = [];

            if ($validator->fails()) {
                $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
            } else {
                $response = ['status'=>1, 'msg'=>''];

                $data = json_decode($data);
                try {
                    if(isset($data->card_description)) {
                        $collection = new Collection();
                        $collection->name = $data->name;
                        $collection->symbol = $data->symbol;
                        $collection->release_date = $data->release_date;
                        $collection->save();

                        $card = new Card();
                        $card->name = $data->card_name;
                        $card->description = $data->card_description;
                        $card->save();

                        $contain = new Contain();
                        $contain->card_id = $card->id;
                        $contain->collection_id = $collection->id;
                        $contain->save();

                        $response['msg'] = "Coleccion creada correctamente con el id ".$collection->id;
                    } else {
                        $cardExists = Card::where('id', $data->card_id)->first();
                        if($cardExists) {
                            $collection = new Collection();
                            $collection->name = $data->name;
                            $collection->symbol = $data->symbol;
                            $collection->release_date = $data->release_date;
                            $collection->save();

                            $contain = new Contain();
                            $contain->card_id = $cardExists->id;
                            $contain->collection_id = $collection->id;
                            $contain->save();

                            $response['msg'] = "Coleccion creada correctamente con el id ".$collection->id;
                        } else {
                            $response['msg'] = "No existe ninguna carta con ese id";
                            $response['status'] = 0;
                        }
                    }
                } catch (\Throwable $th) {
                    $response['msg'] = "Se ha producido un error:".$th->getMessage();
                    $response['status'] = 0;
                }
            }
        } else {
            $response['msg'] = "No se han introducido datos";
            $response['status'] = 0;
        }
        return response()->json($response);
    }
}
