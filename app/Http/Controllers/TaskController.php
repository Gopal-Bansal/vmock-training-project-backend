<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Events\TaskCreate;
use App\Models\Task;
use App\Events\NotificationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;



class TaskController extends Controller
{
    public function create(Request $request)
    {        
        $value = $request->all();  // get all
        //$user = auth()->user(); verfies the user via token

       // return response($value);
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'assigned_to' => 'required',
            'assigned_by' => 'required',
        ]);
        $assigned_to=$value['assigned_to'];
        $assigned_by=$value['assigned_by'];
       // return response($request);
        $user = User::where('id', '=', $request->assigned_to)->first();
        if ($user === null) {
        // user doesn't exist
        return response()->json('Cannot assign task to a non existent user');
        }
        $task = Task::create($value);
        $type="created";
        event(new TaskCreate($user, $task)); //event fired
        event(new NotificationEvent($user, $task, $type));
        $app_id = '1477321';
        $app_key = '6d8be11722c10dd61b44';
        $app_secret = 'e2b13db721d8a785d491';
        $app_cluster = 'ap2';

        $pusher = new Pusher($app_key, $app_secret, $app_id, ['cluster' => $app_cluster]); // new pusher variable created acc to our specifications and pushed into our channel
        $pusher->trigger('my-channel'.$assigned_to, 'my-event', array('message'=>'task created'));
        $pusher->trigger('my-channel'.$assigned_by, 'my-event', array('message'=>'task created'));


       
        return response()->json($task, 201);
    }
    public function update($id, Request $request)
    {
        $task = Task::findOrFail($id);

        
        if ($task['assigned_to'] != $request->userID){
            return response()->json('You have not been assigned this task');
        }
        if ((strtolower($request->status) != 'in-progress' && strtolower($request->status) != 'completed' && strtolower($request->status) != 'assigned' && strtolower($request->status) != 'deleted') ) {
            return response()->json('Status is mandatory to fill, and it can only take four values');
        }
       // $task->fill([
        //    'status' => $request->status,
       // ]);
        $task->status  = $request->status;
       //$task->update($request->only(['status']));
        $task->save();
        // $user = User::where('id', '=', $task->assigned_by)->get();
        $user = User::findOrfail($task->assigned_by);
        
        $type="updated";
        event(new NotificationEvent($user, $task, $type));
        return response()->json($task, 200);
    }
    public function delete($id, Request $request)
    {
        // dd($request->userID);
        $task = Task::findOrFail($id);
        if ($task['assigned_by'] != $request->userID){
            return response()->json('Oops! You are not the creator of this task');
        }
        if ($task != null) {
            // $task->fill([
            //    // 'deleted_by' => strval($request->userID),
            //     'status' => 'deleted',
            // ]);
            $task->status = 'deleted';
        }
        $task->save();

        // $user = User::where('id', '=', $task->assigned_to);
        $user = User::findOrfail($task->assigned_to);

        $type="deleted";
        event(new NotificationEvent($user, $task, $type));
         return response($task, 200);
    }
    public function edittask($id, Request $request)
    {   
        //dd($request);
        $task = Task::findOrFail($id);
       // 

        //return response($user);

         if ($task['assigned_by'] != $request->userID){
             return response()->json('You have not assigned this task');
        }

        if ($request->description == '' && $request->title == '' && $request->due_date == ''){
            return response()->json('One of the three fields atleast needs to be filled');
        }
        if($request->filled('description') )
        {
            $task->update(['description'=>$request->description]);
        }
        if($request->filled('title') )
        {
            $task->update(['title'=>$request->title]);
        }
        if($request->filled('due_date') )
        {
            $task->update(['due_date'=>$request->due_date]);
        }


        // $user = User::where('id', '=', $task->assigned_to);
        $user = User::findOrfail($task->assigned_to);
        $type="edited";
        event(new NotificationEvent($user, $task, $type));
        return response()->json('Updated!');
    }
public function showAllTasks($id){
    $firstCondition = [['assigned_by', $id],['deleted_by',null]];
    $secondCondition = [['assigned_to', $id],['deleted_by',null]];
    $tasks = Task::where($firstCondition)->orWhere($secondCondition)->get();
    return response()->json($tasks);
}
public function showAllTasksAdmin(Request $request) {
    $tasks = Task::all();
    return response()->json($tasks);
}
public function searchTask($input){
    $tasks = Task::where('title', 'like', $input.'%')->orWhere('description','like', $input.'%')->orWhere('assigned_to', 'like', $input.'%')->orWhere('id', 'like', $input.'%')->orWhere('assigned_by', 'like', $input.'%')->orWhere('status', 'like', $input.'%')->get();
    return $tasks;
}
public function searchtaskuser($input, $id){
    // [['title', 'like', $input.'%'],['assigned_by', $id],['deleted_by',null]]
    // [['description','like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['assigned_to', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['id', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['status', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['assigned_by', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]]
    // [['description','like', $input.'%'], ['assigned_to', $id],['deleted_by',null]]
    // [['title', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]]
    // [['assigned_to', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]]
    // [['id', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]]
    // [['status', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]]
    // [['assigned_by', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]]
    $tasks = Task::where([['title', 'like', $input.'%'],['assigned_by', $id],['deleted_by',null]])->orWhere([['description','like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['assigned_to', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])->orWhere([['id', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['status', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])
    ->orWhere([['assigned_by', 'like', $input.'%'], ['assigned_by', $id],['deleted_by',null]])->orWhere([['description','like', $input.'%'], ['assigned_to', $id],['deleted_by',null]])
    ->orWhere([['title', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]])->orWhere([['assigned_to', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]])
    ->orWhere([['id', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]])->orWhere([['status', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]])->
    orWhere([['assigned_by', 'like', $input.'%'], ['assigned_to', $id],['deleted_by',null]])->get();
    return $tasks;
}
public function filtertaskadmin($field, $value){
    $task = Task::where($field, '=', $value)->get();
    return $task;
}
public function sorttaskadmin($field, $order){
    $task = Task::orderBy($field, $order)->get();
    return $task;
}
public function filtertask($field, $value, $id){
    $task = Task::where([[$field, '=', $value],['assigned_by', $id],['deleted_by',null]])->orWhere([[$field, '=', $value], ['assigned_to', $id],['deleted_by',null]])->get();
    return $task;
}
public function sorttask($field, $order, $id){
    $firstCondition = [['assigned_by', $id],['deleted_by',null]];
    $secondCondition = [['assigned_to', $id],['deleted_by',null]];
    $tasks = Task::where($firstCondition)->orWhere($secondCondition)->orderBy($field, $order)->get();
    return $tasks;
}
}







