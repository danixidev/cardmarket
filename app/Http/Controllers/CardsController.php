<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CardsController extends Controller
{
    public function create(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'name' => 'required|unique:cards|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $card = new Card();

                $card->name = $data->name;
                $card->description = $data->description;

                $card->save();

                $response['msg'] = "Carta creada correctamente con el id ".$card->id;
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }
}
