<?php

namespace App\Http\Controllers;

use App\Interfaces\UserInterface;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;

class UserController extends Controller
{
    private UserInterface $userInterface;
    

    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }
    public function index()
    {
        try{
            $data = $this->userInterface->index();
            return ApiResponse::sendResponse(
                true,
                $data,
                "operation effectuer avec success" ? 200 : 401
            );
        }catch(\Throwable $e){
            return ApiResponse::rollback($e);
        }


    }
}