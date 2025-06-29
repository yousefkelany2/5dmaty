<?php

namespace App\Http\Controllers\Api;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::where('approved', true)->latest()->get();


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
            'approved' => false, // لسه محتاج موافقة الأدمن
        ]);

        return response()->json([
            'message' => 'تم إرسال التوصية للمراجعة من الإدارة.',
            'testimonial' => $testimonial,
        ]);
    }

    public function approve(Request $request, $id)
    {

        $request->validate([
            'approved' => 'required|boolean',
        ]);

        $testimonial = Testimonial::findOrFail($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'Testimonial not found.'
            ], 404);
        }
        $testimonial->update(['approved' => $request->approved]);

        $msg = $request->approved ? 'تمت الموافقة على التوصية' : 'تم رفض التوصية';

        return response()->json(['message' => $msg]);
    }
}
