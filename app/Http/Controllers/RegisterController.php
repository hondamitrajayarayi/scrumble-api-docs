<?php

namespace App\Http\Controllers;

use Hash;
use App\Http\Requests\Api\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    /**
     * Register user
     * 
     * Registering new user
     * 
     * @unauthenticated
     * 
     * @param \App\Http\Requests\Api\RegisterRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        // $success['token'] =  $user->createToken(uniqid() . time() . $input['password'])->plainTextToken;
        // $success['auth_type'] =  'bearer token';
        // $success['expires_at'] = Carbon::now()->addMinute()->format('d-m-Y H:i:s');
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
    
}
