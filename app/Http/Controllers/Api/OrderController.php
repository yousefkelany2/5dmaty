<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('service')->latest()->get();
        return response()->json($orders);
    }


    public function store(OrderRequest $request)
    {

        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }
        $service = Service::find($request->service_id);

        if (!$service) {
            return response()->json([
                'message' => 'الخدمة غير موجودة.',
            ], 404);
        }
        $order = Order::create([
            'service_id' => $request->service_id,
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'status' => 'pending',
        ]);




        // توليد رسالة WhatsApp
        $message = "طلب جديد لخدمة: {$service->name_ar}\n"
            . "الاسم: {$request->name}\n"
            . "رقم الهاتف: {$request->phone}\n"
            . "البريد الإلكتروني: " . ($request->email ?? 'غير مذكور');

        $adminPhone = '201065189050'; // رقم الأدمن بصيغة دولية من غير +
        $whatsappUrl = "https://wa.me/{$adminPhone}?text=" . urlencode($message);

        return response()->json([
            'message' => 'تم استلام الطلب بنجاح.',
            'redirect_whatsapp' => $whatsappUrl,
            'order_id' => $order->id,
        ]);
    }

    public function checkStatus($id)
    {
        $order = Order::findOrFail($id);
        if (!$order) {
            return response()->json([
                'message' => 'الطلب غير موجود.',
            ], 404);
        }

        $statusMessages = [
            'pending' => 'طلبك قيد الانتظار ⏳',
            'confirmed' => 'تم تأكيد الطلب ✅',
            'in_progress' => 'طلبك قيد التنفيذ 🔧',
            'completed' => 'تم تنفيذ الطلب بنجاح 🎉',
            'cancelled' => 'تم إلغاء الطلب ❌',
        ];

        $statusMessage = $statusMessages[$order->status] ?? 'حالة الطلب غير معروفة.';
        return response()->json([
            'status' => $order->status,
            'message' => $statusMessage,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {


        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        if (!$order) {
            return response()->json([
                'message' => 'الطلب غير موجود.',
            ], 404);
        }
        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'تم تحديث حالة الطلب']);
    }
    public function myOrders()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        $orders = Order::with('service')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function update(Request $request, $id)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }
        $order = Order::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'الطلب غير موجود.',
            ], 404);
        }

        // تأكد إن الطلب ملك لليوزر ده
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا الطلب.'], 403);
        }

        // نسمحله يعدل البيانات الأساسية (لو محتاجين)
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email',
            'service_id' => 'sometimes|exists:services,id',
        ]);

        $order->update($request->only('name', 'phone', 'email','service_id'));

        return response()->json([
            'message' => 'تم تعديل الطلب بنجاح.',
            'order' => $order
        ]);
    }
    public function destroy($id)
    {
        
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }
        $order = Order::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'الطلب غير موجود.',
            ], 404);
        }

        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذا الطلب.'], 403);
        }

        $order->delete();

        return response()->json(['message' => 'تم حذف الطلب بنجاح.']);
    }
}
