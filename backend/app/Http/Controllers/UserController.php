<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return response()->json(['message' => 'User index teste'], 200);
    }

    // public function create(CreateUserRequest $request){
    //     $validate = $request->validated();

    //     return response()->json(['message' => 'User created successfully', 'data' => $validate], 201);
    // }

    // public function show($id){
    //     return response()->json(['message' => "User details for user with id: $id"], 200);
    // }
}
