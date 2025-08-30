<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::query()->with('category')->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|integer|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $service = Service::create($data);
        return response()->json($service->load('category'), 201);
    }

    public function show(Service $service)
    {
        return $service->load('category');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'category_id' => 'sometimes|required|integer|exists:service_categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'sometimes|required|numeric',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $service->update($data);
        return $service->load('category');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response()->noContent();
    }
}
