<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CollectionsController extends Controller
{
    public function create(Request $req) {
        $data = $req->getContent();

        $validator = Validator::make(json_decode($data, true), [
            'name' => 'required|unique:collections|string',
            'symbol' => 'required|string',
            'release_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);
            try {
                $collection = new Collection();

                $collection->name = $data->name;
                $collection->symbol = $data->symbol;
                $collection->release_date = $data->release_date;

                $collection->save();

                $response['msg'] = "Carta creada correctamente con el id ".$collection->id;
            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error:".$th->getMessage();
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }
}
