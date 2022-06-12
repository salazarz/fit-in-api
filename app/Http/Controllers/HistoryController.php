<?php

namespace App\Http\Controllers;

use App\Models\History;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class HistoryController extends Controller
{
    private $headers = [
        'Content-Type' => 'application/json'
    ];
    //
    public function predict(Request $req)
    {        
        try {
            $req->validate([                
                'food' => 'required',
                'calories' => 'required',
            ]);

            DB::beginTransaction();
            $user = auth('sanctum')->user();
            $data = History::create([        
                'user_id' => $user->id,
                'food' => $req->input('food'),
                'calories' => $req->input('calories'),
                'date' => new DateTime('today'),
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'errmsg' => 'Failed to Predict!',
                'dddd' => $exception->getMessage()
                
            ], 404, $this->headers);
        }
        return response()->json([
            'msg' => 'Success to Predict!',
            'data' => $data,
            
        ], 200, $this->headers);
    }

    public function history(Request $req)
    {        
        try {

            DB::beginTransaction();
            $user = auth('sanctum')->user();
            $data = History::where('user_id', $user->id)->where('date', new DateTime('today'))->get();
            $day_calories = $data->sum('calories');
            $limit_calories = 0;
            if($user->gender == "male"){
                $limit_calories = 66.5+(13.75*$user->weight)+(5.003*$user->height)-(6.75*$user->age);
            }else{
                $limit_calories = 655.1+(9.563*$user->weight)+(1.850*$user->height)-(4.676*$user->age);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'errmsg' => 'Failed to Predict!',
                'dddd' => $exception->getMessage()
                
            ], 404, $this->headers);
        }
        return response()->json([
            'msg' => 'Success to Predict!',
            'data' => [
                'user' => $user,
                'calories' => $day_calories. " cal of ".$limit_calories." cal",
                'history' => $data,
            ],
            
        ], 200, $this->headers);
    }
}
