<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::role('admin')->orderBy('created_at', 'desc')
            ->get();
        return ResponseTrait::success('user retrieved successfully', [
            'users' => $users,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::createUser($request->validated());
            return ResponseTrait::success('User created successfully', [
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the user: ' . $th->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->updateUser($request->validated());
            DB::commit();
            return ResponseTrait::success('User Updated successfully', [
                'user' => $user,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseTrait::error('An error occurred while updating the user: ' . $th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->deleteUser();
            return ResponseTrait::success('User deleted successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while deleting the user: ' . $th->getMessage());
        }
    }
}
