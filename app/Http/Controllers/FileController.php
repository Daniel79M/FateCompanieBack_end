<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Groupe;
use App\Resources\GroupResource;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    public function file(Request $fileRequest, $id)
    {
        $data = [
            'file' => 'required|file|mimes:jpg,png,pdf,docx,txt|max:2048',
            'groupe_id' => 'required|exists:groupes,id'
        ];
    
        // Validation des données
        $validatedData = $fileRequest->validate($data);
    
        DB::beginTransaction();
    
        try {
            $file = $fileRequest->file('file');
            $path = $file->store('ChatFiles', 'public');
    
            $fileModel = new File();
            $fileModel->file_name = $file->getClientOriginalName();
            $fileModel->file_path = $path;
            $fileModel->file_type = $file->getClientMimeType();
            $fileModel->groupe_id = $validatedData['groupe_id'];
            $fileModel->user_id = $id;    
            $fileModel->save(); 
    
            DB::commit();
    
            return ApiResponse::sendResponse(
                true, 
                $fileModel,
                'Opération effectuée',
                201);
        } catch (\Throwable $th) {
            DB::rollback(); 
            // return $th;
            return ApiResponse::rollback($th); 
        }
    }

    public function getGroupFiles($groupeId)
{
    try {
        // Récupérer le groupe par son ID
        $group = Groupe::findOrFail($groupeId);

        // Récupérer tous les fichiers associés à ce groupe
        $files = File::where('groupe_id', $groupeId)->get();

        return ApiResponse::sendResponse(
            true,
            'Fichiers récupérés avec succès.',
            // [new UserResource($user)],
            $files,

            200
        );
    } catch (\Exception $e) {
        return ApiResponse::sendResponse(
            'Erreur lors de la récupération des fichiers.',
            null,
            500
        );
    }
}
}
