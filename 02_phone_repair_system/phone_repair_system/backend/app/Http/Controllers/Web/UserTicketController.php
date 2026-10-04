<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RepairTicket;
use App\Models\RepairStatusLog;
use App\Models\RepairType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserTicketController extends Controller
{
    public function track(Request $request)
    {
        $query = trim($request->input('query', ''));
        $ticket = null;
        $errorMessage = null;

        if ($query !== '') {
            $ticket = RepairTicket::with(['owner', 'repairType', 'statusLogs'])
                ->where('tracking_code', $query)
                ->orWhereHas('owner', function ($q) use ($query) {
                    $q->where('phone', $query);
                })
                ->orderBy('id', 'desc')
                ->first();

            if (!$ticket) {
                $errorMessage = "ไม่พบข้อมูลงานซ่อมสำหรับรหัสติดตามหรือเบอร์โทร: '{$query}'";
            }
        }

        return view('user.track', compact('ticket', 'query', 'errorMessage'));
    }

    public function myTickets()
    {
        $user = Auth::user();
        $tickets = RepairTicket::with(['repairType', 'latestStatusLog', 'statusLogs'])
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.my_tickets', compact('tickets'));
    }

    public function create()
    {
        $repairTypes = RepairType::orderBy('name')->get();
        return view('user.create_ticket', compact('repairTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'repair_type_id' => 'required|exists:repair_types,id',
            'device_model' => 'required|string|max:255',
            'issue_description' => 'required|string|max:1000',
        ], [
            'repair_type_id.required' => 'กรุณาเลือกประเภทการซ่อม/บริการ',
            'device_model.required' => 'กรุณาระบุยี่ห้อและรุ่นของอุปกรณ์',
            'issue_description.required' => 'กรุณากรอกรายละเอียดอาการเสีย',
        ]);

        $user = Auth::user();
        $repairType = RepairType::findOrFail($request->repair_type_id);

        $year = date('Y');
        $count = RepairTicket::count() + 1;
        $trackingCode = sprintf('REP-%s%03d', $year, $count);
        while (RepairTicket::where('tracking_code', $trackingCode)->exists()) {
            $count++;
            $trackingCode = sprintf('REP-%s%03d', $year, $count);
        }

        $ticket = RepairTicket::create([
            'tracking_code' => $trackingCode,
            'user_id' => $user->id,
            'repair_type_id' => $repairType->id,
            'device_model' => $request->device_model,
            'issue_description' => $request->issue_description,
            'status' => 'received',
            'created_at' => Carbon::now(),
        ]);

        RepairStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => 'received',
            'note' => 'ลูกค้าส่งคำขอแจ้งซ่อมผ่านระบบออนไลน์ รอช่างติดต่อกลับและนำเครื่องส่งร้าน',
            'cost' => $repairType->base_price,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('user.my_tickets')
            ->with('success', "ยื่นคำขอแจ้งซ่อมสำเร็จ! รหัสติดตามของคุณคือ: {$ticket->tracking_code}");
    }
}
