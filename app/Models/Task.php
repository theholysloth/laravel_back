<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


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
}
