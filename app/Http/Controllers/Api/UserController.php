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
        $users = User::all();   
        // p($users);
         if(count($users)> 0){
            //user exists
            $response = [
                'message' => count($users) . "users found",
                'status' => 1,
                'data' => $users,
            ];
            return response()->json($response, 200);

         }else{
            //user does not exists
            $response = [
                'message' => count($users) . "users found",
                'status' => 0,
                'data' => $users,
            ];
            return response()->json($response, 200);
         }
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
        $user = User::find($id );
        if(is_null($user)){
            $response = [
                'message' => 'user not found',
                'status' => 0
            ];
        }else{
            $response = [
                'message' => 'user found',
                'status' => 1,
                'data' => $user
            ];
        }
        return response()->json($response,200);
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
        $user = User::find($id);
        if(is_null($user)){
            //does not exist
            return response()->json(
                [
                    'status' =>0,
                    'message' => 'user does not exist'
                ],404
            );
        }else{
            DB::beginTransaction();
            try{
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                ]);
            //user exists
            $user->name = $request['name'];
            $user->email = $request['email'];
            // $user->password = bcrypt($request['password']);
            $user->contact = $request['contact'];
            $user->pincode = $request['pincode'];
            $user->address = $request['address'];
            $user->save();
            DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                $user = null;
            }
            if(is_null($user)){
                return response()->json(
                    [
                        'status'=> 0,
                        'message'=> 'Internal server error',
                        'e_message' => $e ->getMessage()
                    ],500
                );
            }else{
                return response()->json(
                    [
                        'status'=> 1,
                        'message'=> 'user data upadted successfully'
                    ],200
                );
                
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    
        $user = User::find($id);
        if(is_null($user)){
            $response = [
                'message' =>'user does not exist',
                'status' => 0
            ];
            $respcode = 404;

        }
        else{
            DB::beginTransaction();
            try{
                $user->delete();
                DB::commit();
                $response = [
                    'message' =>'user deleted successfully',
                    'status' => 1
                ];
                $respcode = 200;

            }
            catch(\Exception $err){
                DB::rollBack();
                $response = [
                    'message' =>'internal server error',
                    'status' => 0
                ];
                $respcode = 500;

            }

        }
        return response()->json($response, $respcode);
    }

    public function changePassword(request $request , $id){
        $user = User::find($id);
        if(is_null($user)){
            //does not exist
            return response()->json(
                [
                    'status' =>0,
                    'message' => 'user does not exist'
                ],404
            );
        
    }else{
        if($user->password == $request['old_password']){
            //change
            if($request['new_password'] == $request['confirm_password']){
            //change
            DB::beginTransaction();
            try{
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                ]);
            //user exists
            $user->password = $request['new_password'];
            $user->save();
            DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                $user = null;
            }
            if(is_null($user)){
                return response()->json(
                    [
                        'status'=> 0,
                        'message'=> 'Internal server error',
                        'e_message' => $e ->getMessage()
                    ],500
                );
            }else{
                return response()->json(
                    [
                        'status'=> 1,
                        'message'=> 'password upadted successfully'
                    ],200
                );
                
            }
            }else{
                return response()->json(
                    [
                        'status' =>0,
                        'message' => 'new and confirm password does not match'
                    ],404
                );

            }

    }else{
        //thow error
        return response()->json(
            [
                'status' =>0,
                'message' => 'old password does not match'
            ],400
        );
    }
    }
}
