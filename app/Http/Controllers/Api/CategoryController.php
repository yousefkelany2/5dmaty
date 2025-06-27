<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    public function show($id)
    {
        $category = Category::findOrFail($id);
        if (!$category) {
        return response()->json([
            'message' => 'Category not found',
            'code' => 404
        ], 404);
    }
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'image' => $imagePath,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        // 1. هات الكاتيجوري أو ارجع 404
        $category = Category::findOrFail($id);
         if (!$category) {
        return response()->json([
            'message' => 'Category not found',
            'code' => 404
        ], 404);
    }

        // 2. فاليديشن للحقول
        $request->validate([
            'name_ar' => 'sometimes|string',
            'name_en' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // 3. جهز البيانات
        $data = $request->only(['name_ar', 'name_en']);

        // 4. لو فيه صورة جديدة
        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // 🆕 ارفع الصورة الجديدة
            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        // 5. نفذ التعديل
        $category->update($data);

        // 6. رجع الريسبونس
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);
         if (!$category) {
        return response()->json([
            'message' => 'Category not found',
            'code' => 404
        ], 404);
    }

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        // 🗑️ حذف السطر من قاعدة البيانات
        $category->delete();

        return response()->json(['message' => 'Category and its image deleted']);


      
    }
}
