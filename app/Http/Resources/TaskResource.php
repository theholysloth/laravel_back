<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [ //this fait reference au modele Task
            'id' => $this->id,
            'nom' => $this->nom,
            'task' => $this->task,
            'date' => $this->date, 
            'done' => $this->done
        ];
    }
}
