<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use App\Models\Contain;
use App\Models\Offer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardsController extends Controller
{
    public function create(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'name' => 'required|string',
            'description' => 'required|string',
            'collection_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $collection = Collection::where('id', $data->collection_id)->first();
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
            'card_id' => 'required|integer',
            'collection_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $card = Card::where('id', $data->card_id)->first();
                if($card) {
                    $collection = Collection::where('id', $data->collection_id)->first();
                    if($collection) {

                        $contain = new Contain();
                        $contain->card_id = $card->id;
                        $contain->collection_id = $collection->id;
                        $contain->save();

                        $response['msg'] = "Carta añadida a la coleccion correctamente";
                    } else {
                        $response['msg'] = "No existe ninguna coleccion con esa id";
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "No existe ninguna carta con esa id";
                    $response['status'] = 0;
                }
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function search(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'card_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $cards = Card::where('name', 'like', '%'.$data->card_name.'%')->get();
                if(Card::where('name', 'like', '%'.$data->card_name.'%')->first()) {
                    $response['msg'] = "Carta encontrada.";
                    $response['status'] = 1;
                    $response['datos'] = $cards;
                } else {
                    $response['msg'] = "No existe ninguna carta que contenga esa palabra o palabras.";
                    $response['status'] = 0;
                }
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function sell(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'card_id' => 'required|integer',
            'amount' => 'required|integer',
            'price' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $card = Card::where('id', $data->card_id)->first();
                if($card) {
                    $offer = new Offer();
                    $offer->user_id = $req->user->id;
                    $offer->card_id = $data->card_id;
                    $offer->amount = $data->amount;
                    $offer->price = $data->price;
                    $offer->save();

                    $response['msg'] = "Oferta de venta creada correctamente.";
                    $response['status'] = 1;
                } else {
                    $response['msg'] = "No existe ninguna carta con esa id";
                    $response['status'] = 0;
                }
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function searchToBuy(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'card_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $cards = DB::table('offers')->select(['offers.id', 'cards.name', 'offers.amount', 'offers.price', 'users.username'])
                                ->where('name', 'like', '%'.$data->card_name.'%')
                                ->join('users', 'offers.user_id', '=', 'users.id')
                                ->join('cards', 'offers.card_id', '=', 'cards.id')
                                ->orderBy('offers.price', 'asc')
                                ->get();
                if(count($cards) > 0) {
                    $response['msg'] = "Carta encontrada.";
                    $response['status'] = 1;
                    $response['datos'] = $cards;
                } else {
                    $response['msg'] = "No existe ninguna carta con ese nombre.";
                    $response['status'] = 0;
                }
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function buy(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'offer_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $offer = Offer::where('id', $data->offer_id)->first();
                if($offer) {
                    $offer->delete();

                    $response['msg'] = "Carta comprada correctamente.";
                    $response['status'] = 1;
                } else {
                    $response['msg'] = "No existe ninguna oferta con ese id";
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
