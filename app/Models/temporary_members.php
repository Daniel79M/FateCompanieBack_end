<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temporary_members extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupe_id',
        'email',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    // public function groupes()
    // {
    //     return $this->belongsToMany(Groupe::class, 'group_guest_users', 'guest_user_id', 'group_id');
    // }
}
