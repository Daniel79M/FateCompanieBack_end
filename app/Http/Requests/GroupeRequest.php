<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'groupe_name' => 'required|string',
            'groupe_image' => 'nullable|string',
            'Groupe_actu' => 'nullable|string',
            'creator_id' => 'required|integer',
            'group_members' => 'required|array|min:1', // Doit être un tableau avec au moins 2 membres
            'group_members.*' => 'integer|exists:users,id', // Chaque membre doit être un ID d'utilisateur valide            // 'non_registered_members' => 'nullable|array', // Noms des membres non inscrits
            'non_registered_members.*.email' => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [];
    }

    //     public function store(GroupeRequest $request, $id)
// {
//     DB::beginTransaction();
//     try {
//         $request->validated();

//         // Gestion de l'avatar
//         $avatar = "/images/group-avatar.png"; // Image par défaut
//         if ($request->hasFile('groupe_image')) {
//             // Stocke l'image et récupère le chemin
//             $avatar = $request->file('groupe_image')->store('group', 'public');
//         }

//         // Création du groupe
//         $group = Groupe::create([
//             'groupe_name' => $request->groupe_name,
//             'groupe_image' => $avatar,
//             'groupe_actu' => $request->groupe_actu,
//             'creator_id' => $id,
//         ]);

//         // Ajout des membres inscrits
//         $groupMembers = collect($request->group_members)
//             ->map(fn($member_id) => ['member_id' => $member_id, 'groupe_id' => $group->id]);
//         $group->groupe_members()->createMany($groupMembers->toArray());

//         // Ajout des membres non inscrits
//         if ($request->filled('non_registered_members')) {
//             foreach ($request->non_registered_members as $tempMember) {
//                 temporary_members::create([
//                     'name' => $tempMember['name'] ?? null,
//                     'email' => $tempMember['email'],
//                     'phone' => $tempMember['phone'] ?? null,
//                     'groupe_id' => $group->id,
//                 ]);
//             }
//         }

//         DB::commit();

//         return ApiResponse::sendResponse(
//             $group,
//             ["groupMember"],
//             'Groupe créé avec succès.',
//             200
//         );
//     } catch (\Throwable $th) {
//         DB::rollback();
//         return ApiResponse::rollback($th);
//     }
// }
}
