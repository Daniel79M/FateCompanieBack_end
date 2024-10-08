<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupeRequest;
use App\Mail\SendGroupMessage;
use App\Models\File;
use App\Models\Groupe;
use App\Models\Groupe_member;
use App\Models\GroupeNotification;
use App\Models\temporary_members;
use App\Models\User;
use App\Resources\GroupResource;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Expr\Cast\Array_;

use function PHPUnit\Framework\isEmpty;

class GroupeController extends Controller
{

//     public function index()
// {
//     try {
//         // Charger les groupes avec les membres et leurs informations utilisateur (nom, email)
//         $groups = Groupe::with('groupe_members.user')->get();

//         $groupData = $groups->map(function($group) {
//             return [
//                 'group_id' => $group->id,
//                 'group_name' => $group->name,
//                 'member_id' => $group->members->map(function($member) {
//                     return [
//                         'groupe_members' => $member->id,
//                         'name' => $member->user->name,
//                         'email' => $member->user->email
//                     ];
//                 })
//             ];
//         });

//         return ApiResponse::sendResponse(
//             $groupData,
//             null,
//             'Opération effectuée avec succès',
//             200
//         );
//     } catch (\Throwable $th) {
//         DB::rollback();
//         return ApiResponse::rollback($th);
//     }
// }

    public function ShowGroupsForUser(string $user_id)
    {
        try{
            $user = Groupe_member::find($user_id);

        $groupes = [];

        $groupe_id = DB::table('groupe_members')
                        ->where('member_id'
                        , $user_id)->pluck('groupe_id');


        // $userAndGroupe = DB::table('groupe_members')
        //                 ->where('user_id', $id);

        foreach ($groupe_id as $id) {

            array_push($groupes, Groupe::find($id));
        }
            return ApiResponse::sendResponse(

                true,

                $groupes,
                

                'opération effectuer avec succes',
                $groupes ? 200 : 401
            );
        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }

        // if ($user) {
        //     $groups = $user->groupes;
        //     $groupData = $groups->map(function($group) {
        //         return [
        //             'group_id' => $group->id,
        //             'group_name' => $group->name,
        //             'member_id' => $group->members->map(function($user) {
        //                 return [
        //                     'member_id' => $user->id,
        //                     'name' => $user->user->name,
        //                     'email' => $user->user->email
        //                 ];
        //             })
        //         ];
        //     });

        //     return $groupData->toArray();
        // } else {
        //     return ApiResponse::sendResponse(
        //         false,
        //         null,
        //         'Utilisateur introuvable',
        //         404
        //     );
        // }
    }

    public function index()
    {
        try{
            $groups = Groupe::all();

            // Groupe = $groups->name
            // $groupMembers = Groupe_member::all();
            DB::commit();
            return ApiResponse::sendResponse(
                // true, 
                // [new UserResource($user)], 
                // 'Connexion réussie.', 
                // 201
                $groups,
                // $groups->name,
                [
                    $groups
                ],

                'opération effectuer avec succes',
                $groups ? 200 : 401
            );
        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }
    }

    public function addMember(Request $request, $groupeId)
    {
        DB::beginTransaction();
            try {

                // Trouver le groupe par son ID
                $group = Groupe::findOrFail($groupeId);

                // Vérifier si `group_members` est défini et est un tableau
                if ($request->filled('group_members') && is_array($request->group_members)) {
                    // Création d'un tableau pour les membres à ajouter
                    $groupMembersData = collect($request->group_members)
                        ->map(fn($member_id) => ['member_id' => $member_id, 'groupe_id' => $groupeId]);

                    // Ajout des membres au groupe
                    $group->groupe_members()->createMany($groupMembersData->toArray());

                    // Envoi de mails aux membres inscrits
                    foreach ($request->group_members as $member_id) {
                        $member = User::find($member_id);
                        if ($member) {
                            Mail::to($member->email)->send(new SendGroupMessage($group, ['name' => $member->name]));
                        }
                    }
                }

                // Gestion des membres non enregistrés
                if ($request->filled('non_registered_members')) {
                    foreach ($request->non_registered_members as $tempMember) {
                        temporary_members::create([
                            'email' => $tempMember['email'] ?? null,
                            'groupe_id' => $groupeId, // Assurez-vous d'utiliser $groupeId ici
                        ]);

                        // Envoi de mails aux membres non inscrits
                        Mail::to($tempMember['email'])->send(new SendGroupMessage($group, $tempMember));
                    }
                }

        DB::commit();

                return ApiResponse::sendResponse(
                    'Opération effectuée.',
                    $group,
                    ['Membre ajouté avec succès.'],
                    $group ? 200 : 401
                );

                // return response()->json(['message' => 'Group created successfully', 'group_id' => $group->id], 201);
            } catch (\Throwable $th) {
                return $th;
                return ApiResponse::rollback($th);
            }
    }


    public function store(GroupeRequest $request, $user_id)
    {
        DB::beginTransaction();
        try {

            // return $request->group_members;

            $request->validated();

            // if ($request->hasFile("groupe_image")) {
            //     $avatar = move_uploaded_file($_FILES['image']['tmp_name'], 'db/groupes/' . $_FILES['image']['name']);
            //     $avatar = $_FILES['image']['name'];
            // } else {
            //     $avatar = '';
            // }

            // Gestion de l'avatar
            $avatar = "src/public/db/image_default.jpg";
            if ($request->hasFile('groupe_image')) {
                $avatar = move_uploaded_file($request->file('avatar'), 'group');
            }
            // Création du groupe
            $group = Groupe::create([
                'groupe_name' => $request->groupe_name, // Assure-toi que 'groupe_name' est bien reçu et transféré
                'groupe_image' => $avatar,
                'groupe_actu' => $request->groupe_actu,
                'creator_id' => $user_id, //auth()->id()
            ]);

            $data = [
                'groupe_id' => $group->id,
                'member_id' => $user_id
            ];

            Groupe_member::create($data);

            DB::commit();

            return ApiResponse::sendResponse(
                // true, 
                // [new UserResource($user)], 
                // 'Connexion réussie.', 
                // 201
                $group,
                'Groupe crée avec succes.',
                $group ? 201 : 401
            );

            // return response()->json(['message' => 'Group created successfully', 'group_id' => $group->id], 201);
        } catch (\Throwable $th) {
            return $th;
            return ApiResponse::rollback($th);
        }
            
    }


}
