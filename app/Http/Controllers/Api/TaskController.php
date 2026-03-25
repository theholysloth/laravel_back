<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TaskResource::collection(Task::orderBy('id')->simplePaginate()); //colection permet de retourner une liste de plusieurs elements
        
        /*il existe : 
        paginate:  le nombre total d’éléments, le nombre total de pages,la page actuelle,les liens “précédent / suivant”, les liens numérotés
        simplePaginate : Version plus légère du paginator classique. Ne connaît pas le nombre total d’éléments, pas de page suivante ni de precedente
        cursorPaginate:  Le plus performant pour les très gros volumes de données.Utilise un curseur basé sur la dernière ligne lue, Navigation ultra rapide même avec des millions de lignes
                        Nécessite un tri strict (souvent par ID), Pas de pages numérotées
        */}
    
        /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {//grace aux fichiers de validation (formRequest dans Requests), Laravel valide automatiquement avant d'entrer dans le controller 
        $task = Task::create($request->validated());//la classe FormRequest offre validated 

        return response()->json($task,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /*
    public function show($id){
        $task = Task::find($id);
        if(!$task){
            return response()->json(["message:" => "Not Found", 404]);
        }
        return response()->json($task);
    }*/

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task ->update($request->validated());

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete(); 
        return response()->noContent();
    }
}
