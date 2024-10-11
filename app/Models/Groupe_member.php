<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Groupe_member extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'groupe_id',
        'member_id',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function groupeMember()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
// class Groupe_member extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'groupe_id',
//         'member_id',
//     ];

//     // Relation avec le modèle Groupe
//     public function groupe()
//     {
//         return $this->belongsTo(Groupe::class);
//     }

//     // Relation avec le modèle User (membre)
//     public function member()
//     {
//         return $this->belongsTo(User::class, 'member_id');
//     }
// }
