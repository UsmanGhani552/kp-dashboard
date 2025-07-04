<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\EditProfileRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::role('client')->with('packages')
            ->get()
            ->map(function ($client) {
                $client->role = $client->getRoleNames()->first(); 
                unset($client->roles);
                return $client;
            });
        return ResponseTrait::success('Client retrieved successfully', [
            'clients' => $clients,
        ]);
    }

    public function store(StoreClientRequest $request)
    {
        try {
            Client::createClient($request->validated());
            return ResponseTrait::success('Client created successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the client: ' . $th->getMessage());
        }
    }

    public function update(UpdateClientRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $client = Client::findOrFail($id);
            if (isset($validated['package_id'])) {
                $package = Package::findOrFail($validated['package_id']);
            }
            $client->updateClient($validated, $package);
            DB::commit();
            return ResponseTrait::success('Client updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseTrait::error('An error occurred while updating the client: ' . $th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->deleteClient();
            return ResponseTrait::success('client deleted successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while deleting the client: ' . $th->getMessage());
        }
    }

    //client
    public function editProfile(EditProfileRequest $request, $id) {
        try {
            $client = Client::findOrFail($id);
            $client->editProfile($request->validated());
            return ResponseTrait::success('client updated successfully', [
                'client' => $client,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred ' . $th->getMessage());
        }

    }
}
