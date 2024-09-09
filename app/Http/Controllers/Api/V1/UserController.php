<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Controllers\V1\UpdateUserRequest;
use App\Http\Requests\Controllers\V1\UserRequest as V1UserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $users = User::with('tasks')->paginate(10);
        } else {
            $users = User::paginate(10);
        }

        return response()->json(UserResource::collection($users));
    }


    /**
     * Store a newly created user in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(V1UserRequest $request)
    {
        $authUser = Auth::user();

        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Only admins can create users.'], 403);
        }

        $validated = $request->validated();

        $user = User::create($validated);

        return response()->json(new UserResource($user), 201);
    }

    /**
     * Display the specified user.
     *
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->role === 'admin') {
            $user->load('tasks');
            return response()->json(new UserResource($user), 200);
        }

        if ($authUser->role === 'manager' || $authUser->role === 'user') {
            
            return response()->json(new UserResource($user), 200);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  UserRequest  $request
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $authUser = Auth::user();

        if ($authUser->role === 'admin' && isset($request->role)) {
            $validated = $request->validated();
            $user->update($validated);
            return new UserResource($user);
        }

        if ($authUser->id === $user->id) {
            $validated = $request->except(['role']);
            $user->update($validated);
            return new UserResource($user);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * 
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Restore a specified user
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $authUser = Auth::user();

        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::withTrashed()->find($id);

        if ($user) {
            $user->restore();
            return response()->json(['message' => 'User restored successfully']);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
}
