<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    //
    public function signup(Request $request){
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);
       try{
        $data['password'] = Hash::make($request->password);
        $token = Str::random(60);
        $data['token'] = $token;
        $user = User::create($data);
        return response()->json([
            "user" => $user,
            "token" => $token
        ]);
    } catch (\Throwable $th) {
        return response()->json(["message" => $th->getMessage()], 400);
    }


    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {

            $data = User::where('email', $request->email)->first();
            if ($data) {
                if(Hash::check($request->password,$data->password)){
                    $token = Str::random(60);
                    $data->api_token = $token;
                    $data->save();
                    return response()->json([
                        "message" => "Successfully Loged in",
                        "user" => $data,
                        "token" => $token
                    ]);
                }
                else{
                    return response()->json(["errors" => ['password' => ['The password is incorrect']]], 422);

                }

            } else {
                return response()->json(["errors" => ['email' => ['You have to register first']]], 422);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 400);
        }
    }
    public function logout()
    {
        try {
            User::whereId(auth()->user()->id)->update(['api_token' => null]);
            return response()->json(["message" => "User logout successfully"]);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 422);
        }
    }
}
