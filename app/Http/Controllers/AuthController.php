<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest as RequestsLoginRequest;
use App\Http\Requests\RegisterRequest as RequestsRegisterRequest;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Resource\UserResource;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private AuthInterface $authInterface;

    public function __construct(AuthInterface $authInterface)
    {
        $this->authInterface = $authInterface;
    }

    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->login($data);

            DB::commit();

            if (!$user){
                return ApiResponse::sendResponse(
                    // true, 
                    // [new UserResource($user)], 
                    // 'Connexion réussie.', 
                    // 201
                    $user, 
                    [], 
                    'Information de connexion incorrect.', 
                    201 
                );
            }

            return ApiResponse::sendResponse(
                // true, 
                // [new UserResource($user)], 
                // 'Connexion réussie.', 
                // 201
                $user, 
                [], 
                'Connexion réussie.', 
                200
            );

        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }
    }

    public function register(RequestsRegisterRequest $request)
    {
        if ($request->hasFile("image")) {
            move_uploaded_file($_FILES['image']['tmp_name'], 'db/products/' . $_FILES['image']['name']);
            $imageName = $_FILES['image']['name'];
        } else {
            $imageName = '';
        }

        $data = [ 
            'name' => $request->name,
            'firstname' => $request->firstname,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => $request->password,
            'image' => $imageName,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->register($data);

            DB::commit();

            return ApiResponse::sendResponse(
                true, 
                $user,
                [new UserResource($user)], 
                'Opération effectuée.', 
                201
            );

        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }
    }

    

    public function checkOtpCode(Request $request)
    {
        $data = [
            'email' => $request->email,
            'code' => $request->code,
        ];

        DB::beginTransaction();
        try {
            $user = $this->authInterface->checkOtpCode($data);

            if (!$user) {
                return ApiResponse::sendResponse(
                    // true, 
                    // [new UserResource($user)], 
                    // 'Connexion réussie.', 
                    // 201
                    false, 
                    [], 
                    'Code confirmation invalide.', 
                    $user ? 200 : 401
                );
            }

            return ApiResponse::sendResponse(
                true, 
                [new UserResource($user)], 
                'Opération effettuee.', 
                $user ? 200 : 401
            );

            DB::commit();

        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }
    }
}
