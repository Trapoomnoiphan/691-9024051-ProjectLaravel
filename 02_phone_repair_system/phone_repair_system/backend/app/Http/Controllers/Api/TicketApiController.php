<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RepairTicket;
use App\Models\RepairStatusLog;
use App\Models\RepairType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketApiController extends Controller
{
    /**
     * GET /api/repair-types
     */
    public function getRepairTypes(): JsonResponse
    {
        $types = RepairType::orderBy('id', 'asc')->get();
        return response()->json([
            'success' => true,
            'count' => $types->count(),
            'data' => $types,
        ]);
    }

    /**
     * POST /api/repair-types (Admin only)
     */
    public function storeRepairType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
        ]);

        $type = RepairType::create([
            'name' => $validated['name'],
            'base_price' => $validated['base_price'],
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'เพิ่มประเภทงานซ่อมสำเร็จ',
            'data' => $type,
        ], 201);
    }

    /**
     * GET /api/tickets (Admin: all, User: own)
     */
    public function indexTickets(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = RepairTicket::with(['owner:id,name,email,phone', 'repairType:id,name,base_price', 'statusLogs']);

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'count' => $tickets->count(),
            'data' => $tickets,
        ]);
    }

    /**
     * POST /api/tickets (Submit repair ticket)
     */
    public function createTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'repair_type_id' => 'required|exists:repair_types,id',
            'device_model' => 'required|string|max:255',
            'issue_description' => 'required|string|max:1000',
            'initial_note' => 'nullable|string|max:500',
            'estimated_cost' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:users,id',
        ]);

        $currentUser = $request->user();
        $customerId = ($currentUser->role === 'admin' && !empty($validated['customer_id']))
            ? $validated['customer_id']
            : $currentUser->id;

        $repairType = RepairType::findOrFail($validated['repair_type_id']);

        $year = date('Y');
        $count = RepairTicket::count() + 1;
        $trackingCode = sprintf('REP-%s%03d', $year, $count);
        while (RepairTicket::where('tracking_code', $trackingCode)->exists()) {
            $count++;
            $trackingCode = sprintf('REP-%s%03d', $year, $count);
        }

        $ticket = RepairTicket::create([
            'tracking_code' => $trackingCode,
            'user_id' => $customerId,
            'repair_type_id' => $repairType->id,
            'device_model' => $validated['device_model'],
            'issue_description' => $validated['issue_description'],
            'status' => 'received',
            'created_at' => Carbon::now(),
        ]);

        $cost = !empty($validated['estimated_cost']) ? $validated['estimated_cost'] : $repairType->base_price;
        $note = !empty($validated['initial_note']) ? $validated['initial_note'] : 'รับเรื่องแจ้งซ่อมเข้าสู่ระบบเรียบร้อย';

        RepairStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => 'received',
            'note' => $note,
            'cost' => $cost,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกใบแจ้งซ่อมสำเร็จ',
            'data' => $ticket->load(['owner:id,name,email,phone', 'repairType:id,name,base_price', 'statusLogs']),
        ], 201);
    }

    /**
     * GET /api/tickets/{code} (Public tracking by tracking code or phone)
     */
    public function getTicketByCode($code): JsonResponse
    {
        $code = trim($code);

        $ticket = RepairTicket::with(['owner:id,name,email,phone', 'repairType:id,name,base_price', 'statusLogs'])
            ->where('tracking_code', $code)
            ->orWhereHas('owner', function ($q) use ($code) {
                $q->where('phone', $code);
            })
            ->orderBy('id', 'desc')
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => "ไม่พบข้อมูลงานซ่อมสำหรับรหัส '{$code}'",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    /**
     * POST /api/tickets/{id}/status (Admin update status & logs)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:received,repairing,completed,cancelled',
            'note' => 'required|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $ticket = RepairTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลใบแจ้งซ่อม',
            ], 404);
        }

        $ticket->status = $validated['status'];
        $ticket->save();

        $cost = isset($validated['cost']) ? $validated['cost'] : $ticket->total_cost;

        $log = RepairStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => $validated['status'],
            'note' => $validated['note'],
            'cost' => $cost,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะและบันทึกประวัติความคืบหน้าสำเร็จ',
            'data' => $ticket->fresh(['owner:id,name,email,phone', 'repairType:id,name,base_price', 'statusLogs']),
        ]);
    }

    /**
     * GET /api/stats (General Dashboard statistics)
     */
    public function getStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => RepairTicket::count(),
                'received' => RepairTicket::where('status', 'received')->count(),
                'repairing' => RepairTicket::where('status', 'repairing')->count(),
                'completed' => RepairTicket::where('status', 'completed')->count(),
                'cancelled' => RepairTicket::where('status', 'cancelled')->count(),
                'total_repair_types' => RepairType::count(),
            ],
        ]);
    }
}
