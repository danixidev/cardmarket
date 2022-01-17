<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function register(Request $req) {
        $data = $req->getContent();

        //Comprueba los datos introducidos (que el usuario y mail son unicos y que en el rol has introducido un rol valido)
        $validator = Validator::make(json_decode($data, true), [
            'username' => 'required|unique:users|string',
            'email' => 'required|unique:users|string',
            'password' => 'required|string',
            'role' => 'required|in:particular,profesional,administrador',       //['particular', 'profesional', 'administrador']
        ]);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);

            //Comprueba el formato de la dirección de correo, si funciona comprueba el de la contraseña
            if(preg_match('/^[a-zA-Z0-9.-_]{1,30}@[a-zA-Z0-9]{1,10}\.[a-zA-Z]{2,5}$/', $data->email)) {     //Que comience por una palabra seguida de un @, otra palabra, un punto y el dominio
                if(preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,30}$/', $data->password)) {        //Al menos un digito, una letra mayuscula, una minuscula, y que tenga al menos 6 digitos
                    try {
                        $user = new User();

                        //Crea el usuario tras haber comprobado todos los datos
                        $user->username = $data->username;
                        $user->email = $data->email;
                        $user->password = Hash::make($data->password);
                        $user->role = $data->role;

                        $user->save();

                        $response['msg'] = "Usuario creado correctamente con el id ".$user->id;
                    } catch (\Throwable $th) {
                        $response['msg'] = "Se ha producido un error:".$th->getMessage();
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "La contraseña no cumple los requisitos (>6 caracteres, al menos 1 mayuscula, al menos 1 numero)";
                    $response['status'] = 0;
                }
            } else {
                $response['msg'] = "El formato del email no es válido.";
                $response['status'] = 0;
            }
        }
        return response()->json($response);
    }

    public function login(Request $req) {
        $data = $req->getContent();

        // Comprueba los datos que se tienen que introducir
        $validator = Validator::make(json_decode($data, true), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        //Si falla muestra el erorr
        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);

            try {
                $user = User::where('username', $data->username)->first();    //Comprueba si el usuario existe

                if($user) {
                    if(Hash::check($data->password, $user->password)) {     //Si existe comprueba la contraseña introducida
                        $token = Hash::make(now().$user->id);

                        $user->api_token = $token;      //Si coincide inicia sesión creando un token
                        $user->save();

                        $response['data'] = $token;
                        $response['msg'] = "Sesión iniciada correctamente.";
                    } else {
                        $response['msg'] = "La contraseña no coincide.";
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "No hay ningún usuario con ese nombre de usuario.";
                    $response['status'] = 0;
                }

            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error: ".$th->getMessage();
                $response['status'] = 0;
            }

        }
        return response()->json($response);
    }

    public function recover(Request $req) {
        $data = $req->getContent();

        // Comprueba los datos que se tienen que introducir
        $validator = Validator::make(json_decode($data, true), [
            'username' => 'required|string',
            'email' => 'required|string',
        ]);

        //Si falla muestra el erorr
        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            $response = ['status'=>1, 'msg'=>''];

            $data = json_decode($data);

            try {
                $user = User::where('username', $data->username)->first();    //Comprueba si el usuario existe
                if($user) {
                    if($user->email == $data->email) {
                        $password = Str::random(8);

                        $user->password = Hash::make($password);
                        $user->save();

                        $response['data'] = $password;
                        $response['msg'] = "Contraseña cambiada correctamente.";
                    } else {
                        $response['msg'] = "El email no coincide con el del usuario introducido";
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "No hay ningún usuario con ese nombre.";
                    $response['status'] = 0;
                }

            } catch (\Throwable $th) {
                $response['msg'] = "Se ha producido un error: ".$th->getMessage();
                $response['status'] = 0;
            }


        }
        return response()->json($response);
    }

    public function changePassword(Request $req) {
        $response = ['status'=>1, 'msg'=>''];

        $data = $req->getContent();

        //comprueba los datos introducidos
        $validator = Validator::make(json_decode($data, true), [
            'username' => 'required|string',
            'old_password' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = json_decode($data);

        if ($validator->fails()) {
            $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
        } else {
            try {
                $user = User::where('username', $data->username)->first();        //Busca al usuario por el email
                if($user) {
                    if(Hash::check($data->old_password, $user->password)) {     //Comprueba la contraseña antigua, si coincide guarda la nueva codificada
                        if(preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z]{6,30}$/', $data->password)) {        //Al menos un digito, una letra mayuscula, una minuscula, y que tenga al menos 6 digitos
                            $user->password = Hash::make($data->password);
                            $user->save();

                            $response['msg'] = "Contraseña cambiada correctamente.";
                        } else {
                            $response['msg'] = "La contraseña no cumple los requisitos (>6 caracteres, al menos 1 mayuscula, al menos 1 numero)";
                            $response['status'] = 0;
                        }
                    } else {
                        $response['msg'] = "La contraseña antigua no coincide.";
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "El usuario no existe";
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
