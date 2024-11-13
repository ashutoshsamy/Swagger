<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *     title="This My First API Documentation",
 *     version="1.0.0",
 *     description="API documentation for My Application",
 *     @OA\Contact(
 *         email="ashutoshk0089@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Register"}, 
 *     summary="This is user register API",
 *     @OA\RequestBody(
 *         @OA\JsonContent(),
 *         @OA\MediaType(
 *           mediaType="multipart/form-data",
 *           @OA\Schema(
 *             type="object",
 *             required={"name" ,"email" ,"password", "password_confirmation"},
 *             @OA\Property(property="name", type="text", example="Ashutosh"),
 *             @OA\Property(property="email", type="text", example="ashutoshk0089@gmail.com"),
 *             @OA\Property(property="password", type="password", example="ashu@123"),
 *             @OA\Property(property="password_confirmation", type="password", example="ashu@123")
 *         ),
 *       ),
 *     ),
 *     @OA\Response(
 *           response=201, 
 *           description="User Registered Successfully",
 *           @OA\JsonContent()
 *      ),
 *     @OA\Response(response=400, description="Bad request"),
 *     @OA\Response(response=404, description="Resource not found "),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */   


public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|confirmed', //password confirmation
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json(
        ['status' => true,
         'user' => $user,
         'message' => "User Registered Successfully",
         'token' => $token], 
        201);
}
/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Login"}, 
 *     summary="This is user login API",
 *     @OA\RequestBody(
 *         @OA\JsonContent(),
 *         @OA\MediaType(
 *           mediaType="multipart/form-data",
 *           @OA\Schema(
 *             type="object",
 *             required={"email" ,"password"},
 *             @OA\Property(property="email", type="text", example="ashutoshk0089@gmail.com"),
 *             @OA\Property(property="password", type="password", example="ashu@123")
 *         ),
 *       ),
 *     ),
 *     @OA\Response(
 *           response=201, 
 *           description="User Login Successfully",
 *           @OA\JsonContent()
 *      ),
 *     @OA\Response(response=400, description="Bad request"),
 *     @OA\Response(response=404, description="Resource not found "),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */ 
public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
        'status' => true,
        'user'   => $user,
        'message'=> "User Logged Successfully",
        'token'  => $token],    
       201);
    }
}