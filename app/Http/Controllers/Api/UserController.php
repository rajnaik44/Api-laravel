<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([  
        //     "name"=> ['required'],
        //     "email"=> ['required', 'email'],
        //     "password"=> ['required','min:8']
        //  ]);


        // validation 
        $validator = Validator::make($request->all(), [
            "name"=> ['required'],
            "email"=> ['required', 'email','unique:users,email,'],
            "password"=> ['required','min:8','confirmed'],
            "password_confirmation" => ["required"]

        ]) ;
        if ($validator->fails()) {
    return response()->json($validator->messages(),400);
        }
        else{
            $data =[
                'name' => $request->name,
                'email' => $request->email,
                'pasword'=> Hash::make($request->password),
            ];
            p($data);
            DB::beginTransaction();
            try {
                $user = User::create($data);
                DB::commit();
            } catch (\Exception $e) {
                p($e->getMessage());
                $user = null;
        }
        if ($user != null) {
            //okey
            return response()->json([
                'message'=>'user register successfully'
            ],200);
        }else{
            // not ok
            return response()->json([
                'message'=> 'Internal server error'
            ],500);
        }
    }}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
