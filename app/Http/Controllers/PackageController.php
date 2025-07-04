<?php

namespace App\Http\Controllers;

use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Models\Package;
use App\Models\PackageDeliverables;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('category','deliverables')->get();
        return ResponseTrait::success('Packages retrieved successfully', [
            'packages' => $packages,
        ]);
    }
    public function store(StorePackageRequest $request)
    {
        try {
            DB::beginTransaction();
            $package = Package::createPackage($request->validated());
            if($request->has('deliverables')) {
                PackageDeliverables::createPackageDeliverables($package, $request->validated()['deliverables']);
            }
            DB::commit();
            return ResponseTrait::success('Package created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseTrait::error('An error occurred due to:  '.$e->getMessage());
        }
    }
    public function update(UpdatePackageRequest $request,$id)
    {
        try {
            DB::beginTransaction();
            $package = Package::findOrFail($id);
            $package->updatePackage($request->validated());
            // PackageDeliverables::createPackageDeliverables($package);
            DB::commit();
            return ResponseTrait::success('Package updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseTrait::error('An error occurred due to: '.$e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $package = Package::findOrFail($id);
            $package->deletePackage();
            return ResponseTrait::success('Package deleted successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while deleting the package: ' . $th->getMessage());
        }
    }

    public function show($id) {
        $package = Package::with('category', 'deliverables')->findOrFail($id);
        return ResponseTrait::success('Package retrieved successfully', [
            'package' => $package,
        ]);
    }

}
