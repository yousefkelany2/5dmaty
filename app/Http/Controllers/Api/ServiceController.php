<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        if (! $service) {
            return response()->json(['message' => 'Service not found.'], 404);
        }


        return response()->json($service);
    }

    public function store(ServiceRequest $request)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $tags = is_string($request->tags) ? json_decode($request->tags, true) : $request->tags;

        if (!is_array($tags)) {
            return response()->json(['message' => 'Invalid tags format. Must be JSON array.'], 422);
        }

        $service = Service::create([
            'category_id' => $request->category_id,
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'price' => $request->price,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'image' => $imagePath,
            'tags' => $tags,
        ]);

        return response()->json([
            'message' => 'Service created successfully.',
            'data' => $service
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        if (! $service) {
            return response()->json(['message' => 'Service not found.'], 404);
        }


        $request->validate([
            'name_ar' => 'sometimes|string',
            'name_en' => 'sometimes|string',
            'price' => 'nullable|numeric',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image',
            'tags' => 'nullable',
        ]);

        $data = $request->except('image', 'tags');

        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        if ($request->has('tags')) {
            $tags = is_string($request->tags) ? json_decode($request->tags, true) : $request->tags;

            if (!is_array($tags)) {
                return response()->json(['message' => 'Invalid tags format. Must be JSON array.'], 422);
            }

            $data['tags'] = $tags;
        }

        $service->update($data);

        return response()->json([
            'message' => 'Service updated successfully.',
            'data' => $service
        ]);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        if (! $service) {
            return response()->json(['message' => 'Service not found.'], 404);
        }


        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully.']);
    }
}
