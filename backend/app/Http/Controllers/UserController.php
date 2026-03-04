<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        $users = User::get();

        return response()->json(['message' => "Usuários recuperados com sucesso", 'data' => $users], 200);
    }

    // public function create(CreateUserRequest $request){
    //     $validate = $request->validated();

    //     return response()->json(['message' => 'User created successfully', 'data' => $validate], 201);
    // }

    // public function show($id){
    //     return response()->json(['message' => "User details for user with id: $id"], 200);
    // }
}
