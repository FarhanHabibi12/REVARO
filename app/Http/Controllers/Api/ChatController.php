<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Send an in-app chat message for an order.
     */
    public function sendMessage(Request $request, Order $order)
    {
        $user = $request->user();

        // Authorize that the user is the customer or technician of the order
        $isCustomer = $user->isPelanggan() && $user->pelanggan && $order->pelanggan_id === $user->pelanggan->id;
        $isTechnician = $user->isTeknisi() && $user->teknisi && $order->teknisi_id === $user->teknisi->id;

        if (!$isCustomer && !$isTechnician) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'pesan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chat = Chat::create([
            'order_id' => $order->id,
            'dari_user_id' => $user->id,
            'pesan' => $request->pesan,
        ]);

        return response()->json($chat->load('dariUser'), 201);
    }

    /**
     * Retrieve chat history for an order.
     */
    public function getMessages(Request $request, Order $order)
    {
        $user = $request->user();

        // Authorize access
        $isCustomer = $user->isPelanggan() && $user->pelanggan && $order->pelanggan_id === $user->pelanggan->id;
        $isTechnician = $user->isTeknisi() && $user->teknisi && $order->teknisi_id === $user->teknisi->id;
        $isAdmin = $user->isSuperAdminPemilik() || $user->isAdminResolusi();

        if (!$isCustomer && !$isTechnician && !$isAdmin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $messages = Chat::where('order_id', $order->id)
            ->with('dariUser')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
