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


    $order = Order::create([
        'service_id' => $request->service_id,
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'status' => 'pending',
    ]);

    $service = Service::find($request->service_id);

    if (!$service) {
        return response()->json([
            'message' => 'الخدمة غير موجودة.',
        ], 404);
    }

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

    $statusMessage = $order->status === 'completed'
        ? 'تم تنفيذ الطلب بنجاح ✅'
        : 'طلبك قيد التنفيذ... ⏳';

    return response()->json([
        'status' => $order->status,
        'message' => $statusMessage,
    ]);
}

public function updateStatus(Request $request, $id)
{


    $request->validate([
        'status' => 'required|in:pending,completed',
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


}
