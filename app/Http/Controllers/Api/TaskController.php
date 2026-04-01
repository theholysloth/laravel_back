<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizesRequests;
    
    public function index(Request $request)
    {
        $user = $request->user(); 
        if ($user->can('view all tasks')){
            return TaskResource::collection(Task::latest()->paginate());
        }else{
            return TaskResource::collection(
            $request->user()->tasks()->latest()->paginate()
            //Task::orderBy('id')->simplePaginate()
            ); //colection permet de retourner une liste de plusieurs elements
        }
        
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
        //$task = Task::create($request->validated());//la classe FormRequest offre validated 
        $task = $request->user()->tasks()->create(
            $request->validated()
        ); 
        return response()->json($task,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);//laravel demande à la policy, est ce que l'user a le droit de ...
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
        $this->authorize('update',$task);
        $validated = $request->validated();
        $task ->update($validated);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete',$task);
        $task->delete(); 
        return response()->noContent();
    }
}
