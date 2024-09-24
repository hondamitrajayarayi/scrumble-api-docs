<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Validator;

class AuthenticationController extends BaseController
{
    /**
     * Get authentication token
     * 
     * Otentikasi yang dipakai API:
     * 
     * - email
     * 
     * - password 
     * 
     * Setiap user yang ingin mengakses API ini memerlukan `email` dan `password` yang akan diinfo oleh develover.
     * 
     * @unauthenticated
     * 
     * @param \App\Http\Requests\Api\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $token = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], expiresAt: Carbon::now()->addMinute(config('sanctum.ac_expiration')))->plainTextToken;
            $refresh = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], expiresAt: Carbon::now()->addMinute(config('sanctum.rt_expiration')))->plainTextToken;

            $success['access_token'] =  explode('|', $token)[1];
            $success['refresh_token'] = explode('|', $refresh)[1];
            $success['auth_type'] =  'bearer token';
            $success['expires_at'] = Carbon::now()->addMinute(config('sanctum.ac_expiration'))->format('d-m-Y H:i:s');
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
    /**
     * Remove authentication token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Refresh token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], expiresAt: Carbon::now()->addMinute(config('sanctum.ac_expiration')))->plainTextToken;

        $success['access_token'] =  explode('|', $token)[1];
        $success['auth_type'] =  'bearer token';
        $success['expires_at'] = Carbon::now()->addMinute(config('sanctum.ac_expiration'))->format('d-m-Y H:i:s');
   
        return $this->sendResponse($success, 'Refresh token successfully.');

    }
}
