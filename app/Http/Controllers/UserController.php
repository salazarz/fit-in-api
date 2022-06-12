<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\User;


class UserController extends Controller
{
    private $headers = [
        'Content-Type' => 'application/json'
    ];
    //

    public function register(Request $req)
    {        
        try {
            $req->validate([
                'name' => 'required',
                'weight' => 'required',
                'height' => 'required',
                'gender' => 'required',
                'age' => 'required',
                'email' => 'required|email',                
                'password' => 'required',
            ]);

            DB::beginTransaction();
            $data = User::create([
                'name' => $req->input('name'),
                'weight' => $req->input('weight'),
                'height' => $req->input('height'),
                'gender' => $req->input('gender'),
                'age' => $req->input('age'),
                'email' => strtolower($req->input('email')),
                'password' => Hash::make($req->input('password')),
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'errmsg' => 'Failed to create an Account!',
                // 'dddd' => $exception->getMessage()
                
            ], 404, $this->headers);
        }
        return response()->json([
            'msg' => 'Success to create an Account!',
            'data' => $data,
            
        ], 200, $this->headers);
    }

    public function login(Request $req)
    {        
        try {
            $req->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $data = User::where('email', strtolower($req->input('email')))->first();

            if(!$data || !Hash::check($req->input('password'), $data->password)){
                throw ValidationException::withMessages(['user' => 'Username atau Password Salah!']);
            }

            $token = $data->createToken('userToken')->plainTextToken;
        } catch (\Exception $exception) {
            return response()->json([
                'errmsg' => 'User Login Failed!',
                'success' => false,
            ], 404, $this->headers);
        }
        return response()->json([
            'msg' => 'User Login Success!',
            'success' => true,
            'data' => [
                'user' => $data,
                'token' => $token,
            ],
            
        ], 200, $this->headers);
    }

    public function logout(Request $req)
    {
        try {
            auth('sanctum')->user()->currentAccessToken()->delete();
        } catch (\Exception $exception) {
            // return $this->onError('', $exception->getMessage());
            return response()->json([
                'errmsg' => 'User Logout Failed!',
                
            ], 404, $this->headers);
        }
        
        return response()->json([
            'msg' => 'User Logout Success!',            
            
        ], 200, $this->headers);
    }
}
