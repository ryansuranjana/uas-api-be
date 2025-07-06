<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        try {
            $users = User::all();

            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching users.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the user.'], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the user.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|required',
            'email' => 'required|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|min:6',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($request->has('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->save();

            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the user.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
        }
    }
}
