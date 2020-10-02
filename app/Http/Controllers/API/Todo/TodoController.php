<?php

namespace App\Http\Controllers\API\Todo;

use App\Http\Controllers\Controller;
use App\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TodoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $todos = $this->fetchTodo();
            return response(['todo'=>$todos, 'success' => true], 200);
        }
        catch(\Exception $exception){
            return response(['error' => 'Unauthorized Access', 'success' => false], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Todo $todo)
    {
        try {
            $validator =  Validator::make($request->all(),[
                'todo' => 'bail|required|min:3|max:50|unique:todos'
            ]);
            if ($validator->fails()){
                $data = [
                    'errors' =>$validator->errors(),
                ];
                return response($data,422);
            }
            $todo->createTodo($request);

            $todos = $this->fetchTodo();
            return response(['todo'=>$todos, 'success' => true], 200);
        }
        catch (\Exception $exception){
            $data = [
                'error' => 'Action could not be completed',
            ];
            return response($data,500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator =  Validator::make($request->all(),[
                'todo' => 'bail|required|min:3|max:50',
                'isComplete' => 'bail|required'
            ]);
            if ($validator->fails()){
                $data = [
                    'errors' =>$validator->errors(),
                ];
                return response($data,422);
            }
            $todo = Todo::where(['user_id' => Auth::user()->id, 'id' => $id])->first();
            if (!$todo){
                return response(['error' => 'Todo does not exist'], 401);
            }
            $todo->update([
                'isComplete' => $request->isComplete,
                'todo' => $request->todo,
            ]);

            $todos = $this->fetchTodo();
            return response(['todo'=>$todos, 'success' => true], 200);
        }
        catch (\Exception $exception){
            $data = [
                'error' => 'Action could not be completed',
            ];
            return response($data,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $todo = Todo::where(['user_id' => Auth::user()->id, 'id' => $id])->first();
            if (!$todo){
                return response(['error' => 'Todo does not exist'] , 401);
            }
            if($todo->delete()){
                $todos = $this->fetchTodo();
                return response(['todo'=>$todos, 'success' => true], 200);
            }
            else{
                return response(['error' => 'Todo could not be deleted'] , 400);
            }
        }
        catch(\Exception $exception){
            return response(['error' => 'Action could not be performed'] , 500);
        }
    }

    public function fetchTodo (){
        return Todo::where('user_id', Auth::user()->id)->get();
    }
}
