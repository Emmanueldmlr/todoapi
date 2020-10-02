<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Todo extends Model
{
    protected $fillable = [
        'user_id', 'todo', 'isComplete'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function createTodo($request){
        $this->user_id = Auth::user()->id;
        $this->todo = $request->todo;
        $this->isComplete = 0;
        $this->save();
        return $this;
    }
}
