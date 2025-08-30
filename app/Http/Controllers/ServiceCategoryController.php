<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        return ServiceCategory::query()->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category = ServiceCategory::create($data);
        return response()->json($category, 201);
    }

    public function show(ServiceCategory $service_category)
    {
        return $service_category;
    }

    public function update(Request $request, ServiceCategory $service_category)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $service_category->update($data);
        return $service_category;
    }

    public function destroy(ServiceCategory $service_category)
    {
        $service_category->delete();
        return response()->noContent();
    }
}
