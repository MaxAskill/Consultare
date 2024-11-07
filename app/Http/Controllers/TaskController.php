<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class TaskController extends Controller
{
    /**
     * List all tasks.
     *

     */
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return response()->json($tasks);
    }

    /**
     * Create a new task resource.
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task = new Task($request->all());
        $task->timestamp = now();
        $task->save();

        return response()->json($task, 201);
    }



    /**
     * Update a single task.

     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $task->fill($request->all());
        $task->timestamp = now();
        $task->save();

        return response()->json($task);
    }

    /**
     * Delete a single task.
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }


}
