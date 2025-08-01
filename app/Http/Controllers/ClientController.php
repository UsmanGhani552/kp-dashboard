<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\EditProfileRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\ClientAssignedPackage;
use App\Models\Package;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Assign;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::role('client')->with('packages')->orderBy('id', 'desc')
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
            $client = Client::createClient($request->validated());
            return ResponseTrait::success('Client created successfully', [
                'client' => $client,
            ]);
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
            $package = null;
            if (isset($validated['package_id'])) {
                $package = Package::findOrFail($validated['package_id'])->toArray();
                $alreadyAssigned = ClientAssignedPackage::where('client_id', $client->id)->where('package_id', $package['id'])->first();
                if ($alreadyAssigned) {
                    return ResponseTrait::error('Package Already Assigned');
                }
            }
            $client->updateClient($validated, $package);
            DB::commit();
            return ResponseTrait::success('Client updated successfully', [
                'client' => $client,
            ]);
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

    public function assignedPackages($id)
    {
        try {
            $client = ClientAssignedPackage::where('client_id', $id)
                ->with(['package.category', 'package.deliverables','invoice'])->orderBy('id', 'desc')
                ->get();
            return ResponseTrait::success('Assigned Packages', [
                'assigned_packages' => $client,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while retrieving assigned packages: ' . $th->getMessage());
        }
    }

    //client
    public function editProfile(EditProfileRequest $request)
    {
        try {
            $client = auth()->user();
            $client->editProfile($request->validated());
            $client->load('clientEmails');
            return ResponseTrait::success('client updated successfully', [
                'client' => $client,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred ' . $th->getMessage());
        }
    }
}
