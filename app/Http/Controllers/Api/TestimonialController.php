<?php

namespace App\Http\Controllers\Api;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::where('approved', 1)->latest()->get();

        return response()->json($testimonials);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        $user = auth('api')->user();


        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        $testimonial = Testimonial::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'content' => $request->content,
            'rating' => $request->rating,
            'approved' => 0, // لسه محتاج موافقة الأدمن
        ]);

        return response()->json([
            'message' => 'تم إرسال التوصية للمراجعة من الإدارة.',
            'testimonial' => $testimonial,
        ]);
    }

    public function approve(Request $request, $id)
    {

        $request->validate([
        'approved' => 'required|in:0,1,2', // 0: pending, 1: approved, 2: rejected
    ]);


        $testimonial = Testimonial::findOrFail($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'Testimonial not found.'
            ], 404);
        }
      $testimonial->update(['approved' => $request->approved]);

    $statusMessages = [
        0 => 'تم تحويل التوصية إلى قيد الانتظار.',
        1 => 'تمت الموافقة على التوصية ✅',
        2 => 'تم رفض التوصية ❌',
    ];

    $msg = $statusMessages[$request->approved] ?? 'تم تحديث الحالة.';

    return response()->json(['message' => $msg]);
    }

    public function myTestimonials()
{
    $user = auth('api')->user();

    if (!$user) {
        return response()->json(['message' => 'يجب تسجيل الدخول'], 401);
    }

    $testimonials = Testimonial::where('user_id', $user->id)->latest()->get();

    return response()->json($testimonials);
}


public function all()
{
    $testimonials = Testimonial::with('user')->latest()->get(); // لو عامل علاقة user في النموذج

    return response()->json($testimonials);
}
public function update(Request $request, $id)
{
    $user = auth('api')->user();
    $testimonial = Testimonial::findOrFail($id);

    // لو الميزة مخصصة لليوزر فقط يعدل توصيته
    if ($testimonial->user_id !== $user->id) {
        return response()->json(['message' => 'غير مصرح لك بالتعديل على هذه التوصية.'], 403);
    }

    $request->validate([
        'content' => 'sometimes|required|string|max:1000',
        'rating' => 'sometimes|required|integer|min:1|max:5',
    ]);

    $testimonial->update($request->only('content', 'rating'));

    return response()->json([
        'message' => 'تم تحديث التوصية بنجاح.',
        'testimonial' => $testimonial,
    ]);
}
public function destroy($id)
{
    $user = auth('api')->user();
    $testimonial = Testimonial::findOrFail($id);

    // لو مستخدم عادي فقط يحذف بتاعه
    if ($user->id !== $testimonial->user_id && !$user->is_admin) {
        return response()->json(['message' => 'غير مصرح لك بحذف هذه التوصية.'], 403);
    }

    $testimonial->delete();

    return response()->json(['message' => 'تم حذف التوصية بنجاح.']);
}

}
