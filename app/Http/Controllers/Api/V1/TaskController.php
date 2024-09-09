<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Controllers\V1\TaskRequest;
use App\Http\Requests\Controllers\V1\UpdateTaskRequest;
use App\Http\Resources\Api\V1\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
     public function __construct()
     {
        $this->middleware('auth:api');
     }

     /**
     * Display a listing of the tasks.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $tasksQuery = Task::query();

        if ($user->role === 'admin') {
            $tasksQuery->withTrashed();
        }

        if ($user->role === 'manager') {
            $tasksQuery->where('created_by', $user->id)
                    ->whereNull('deleted_at');
        }

        if ($user->role === 'user') {
            $tasksQuery->where('assigned_to', $user->id)
                    ->whereNull('deleted_at');
        }

        $priority = $request->priority;
        $status = $request->status;

        if ($priority) {
            $tasksQuery->priority($priority);
        }

        if ($status) {
            $tasksQuery->status($status);
        }

        $tasks = $tasksQuery->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found.'
            ], 403);
        }

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task in storage.
     *
     * @param TaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function store(TaskRequest $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->role !== 'manager') {
            return response()->json([
                'message' => 'Only admins and managers can create tasks.'
            ], 403);
        }

        $validated = $request->validated();
        $validated['created_by'] = $user->id;

        $task = Task::create($validated);

        return response()->json(['task' => new TaskResource($task)], 201);
    }




    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return new TaskResource($task);
        }

        if ($user->role === 'manager') {
            if ($task->created_by === $user->id && !$task->trashed()) {
                return new TaskResource($task);
            }
            return response()->json(['message' => 'You are not authorized to view this task.'], 403);
        }

        if ($user->role === 'user') {
            if ($task->assigned_to === $user->id && !$task->trashed()) {
                return new TaskResource($task);
            }
            return response()->json(['message' => 'You are not authorized to view this task.'], 403);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }


    /**
     * Update the specified task in storage.
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $user = Auth::user();
        $validated = $request->validated();

        if ($user->role === 'admin'){
            $task->update($validated);
            return response()->json(['task' => new TaskResource($task)], 200);
        }

        if ($user->role === 'manager') {
            if ($task->created_by !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized to update this task.'
                ], 403);
            }

            $task->update($validated);

            return response()->json(['task' => new TaskResource($task)], 200);
        }

        if ($user->role === 'user') {
            if ($task->assigned_to !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized to update this task.'
                ], 403);
            }

            if (count($validated) > 1 || !isset($validated['status'])) {
                return response()->json([
                    'message' => 'Users can only update the status of tasks.'
                ], 400);
            }

            $task->update($validated);

            return response()->json(['task' => new TaskResource($task)], 200);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    /**
     * Assign a task to a user
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($user->role === 'manager' && $task->created_by !== $user->id) {
            return response()->json([
                'message' => 'Managers can only assign tasks they have created.'
            ], 403);
        }

        if ($user->role !== 'manager' && $user->role !== 'admin') {
            return response()->json([
                'message' => 'Only managers and admins can assign tasks.'
            ], 403);
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $task->assigned_to = $validated['assigned_to'];
        $task->save();

        return response()->json([
            'message' => 'Task successfully assigned.',
            'task' => new TaskResource($task)
        ], 200);
    }


    /**
 * Remove the specified task from storage.
 *
 * @param Task $task
 * @return JsonResponse
 */
    public function destroy(Task $task)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $task->delete();
            return response()->json([
                'message' => 'Task deleted successfully.'
            ], 200);
        }

        if ($user->role === 'manager') {
            if ($task->created_by === $user->id) {
                $task->delete();
                return response()->json([
                    'message' => 'Task deleted successfully.'
                ], 200);
            }
            return response()->json([
                'message' => 'You are not authorized to delete this task.'
            ], 403);
        }

        if ($user->role === 'user') {
            return response()->json([
                'message' => 'You are not authorized to delete tasks.'
            ], 403);
        }
    
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    /**
     * Restore the specified soft-deleted task.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore($id)
    {
        $user = Auth::user();

        $task = Task::withTrashed()->find($id);

        if (!$task || !$task->trashed()) {
            return response()->json(['message' => 'Task not found or is not deleted.'], 404);
        }

        if ($user->role === 'admin') {
            $task->restore();
            return response()->json(['message' => 'Task restored successfully.'], 200);
        }

        if ($user->role === 'manager') {
            if ($task->created_by !== $user->id) {
                return response()->json(['message' => 'You are not authorized to restore this task.'], 403);
            }

            $task->restore();
            return response()->json(['message' => 'Task restored successfully.'], 200);
        }

        if ($user->role === 'user') {
            return response()->json(['message' => 'You are not authorized to restore tasks.'], 403);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

}
