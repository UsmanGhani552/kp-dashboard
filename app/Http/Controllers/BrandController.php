<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index() {
        $brands = Brand::all();
        return ResponseTrait::success('Brands retrieved successfully',[
            'brands' => $brands,
        ]);
    }
    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = Brand::createBrand($request->validated());
            return ResponseTrait::success('Brand created successfully', [
                'brand' => $brand,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while creating the brand: ' . $th->getMessage());
        }
    }
    public function update(UpdateBrandRequest $request,$id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->updateBrand($request->validated());
            return ResponseTrait::success('Brand updated successfully', [
                'brand' => $brand,
            ]);
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while updating the brand: ' . $th->getMessage());
        }
    }
    public function delete($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->deleteBrand();
            return ResponseTrait::success('Brand deleted successfully');
        } catch (\Throwable $th) {
            return ResponseTrait::error('An error occurred while deleting the brand: ' . $th->getMessage());
        }
    }
}
