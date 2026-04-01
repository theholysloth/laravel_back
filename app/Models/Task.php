<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;


class Task extends Model
{
    use HasFactory;//important pour que le factory soit used 
    use HasUuids; 
    //
    protected $keyType = 'string';//les deux lignes sont importantes popur configurer l'uuid
    public $incrementing = false;
    
    protected $fillable = [
        'nom',
        'task',
        'date', 
        'done'
    ];

    public function user(){//grace à ces realtions on pourra juste faire task->user() pour avoir l'user chargé de ces taches 
        return $this->belongsTo(Task::class);
    }
}
