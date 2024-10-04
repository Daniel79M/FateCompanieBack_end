<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupe_name',
        'creator_id',
        'groupe_image',
        'groupe_actu',
        'member_id'
    ];

    public function creator() 
    {
        return $this->belongsTo(User::class, 'creator_id')->select('id', 'name');
    }

    public function groupe_members() 
    {
        return $this->hasMany(Groupe_member::class, 'groupe_id');
    }
}
