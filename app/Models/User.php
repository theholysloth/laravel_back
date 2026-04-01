<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; //ce trait est necessaire car il permet l'authentification API et la creation de tokens personnels
use Spatie\Permission\Traits\HasRoles; 

use App\Models\Task;
//#[Fillable(['name', 'email', 'password'])]
//#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{   //admin, user = roles
    //permissions : view all tasks, delete any task
    //super-admin : view users, assign roles, assign permissions
    /** @use HasFactory<UserFactory> */
    use HasRoles, HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email', 
        'password'
    ] ; 

    protected $hidden = [
        'password', 
        'remember_token'
    ];

    public function tasks(){//grace à ces realtions on pourra juste faire user->tasks() pour avoir les taches associées à cet user
        return $this->hasMany(Task::class); 
    }

}
