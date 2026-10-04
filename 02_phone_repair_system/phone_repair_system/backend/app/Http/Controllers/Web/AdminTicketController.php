<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RepairTicket;
use App\Models\RepairStatusLog;
use App\Models\RepairType;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminTicketController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = RepairTicket::with(['owner', 'repairType', 'latestStatusLog']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('id', 'desc')->get();

        $stats = [
            'total' => RepairTicket::count(),
            'received' => RepairTicket::where('status', 'received')->count(),
            'repairing' => RepairTicket::where('status', 'repairing')->count(),
            'completed' => RepairTicket::where('status', 'completed')->count(),
            'cancelled' => RepairTicket::where('status', 'cancelled')->count(),
            'total_revenue' => RepairStatusLog::where('status', 'completed')->sum('cost'),
        ];

        return view('admin.dashboard', compact('tickets', 'stats'));
    }

    public function create()
    {
        $customers = User::where('role', 'user')->orderBy('name')->get();
        $repairTypes = RepairType::orderBy('name')->get();

        return view('admin.tickets.create', compact('customers', 'repairTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_type' => 'required|in:existing,new',
            'repair_type_id' => 'required|exists:repair_types,id',
            'device_model' => 'required|string|max:255',
            'issue_description' => 'required|string|max:1000',
            'initial_note' => 'nullable|string|max:500',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        if ($request->customer_type === 'new') {
            $request->validate([
                'new_name' => 'required|string|max:255',
                'new_phone' => 'required|string|unique:users,phone|max:20',
                'new_email' => 'required|email|unique:users,email|max:255',
            ], [
                'new_name.required' => 'กรุณากรอกชื่อลูกค้าใหม่',
                'new_phone.unique' => 'เบอร์โทรศัพท์นี้ถูกใช้งานแล้ว',
                'new_email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            ]);

            $customer = User::create([
                'name' => $request->new_name,
                'phone' => $request->new_phone,
                'email' => $request->new_email,
                'password' => Hash::make('user1234'),
                'role' => 'user',
            ]);
            $customerId = $customer->id;
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ], [
                'user_id.required' => 'กรุณาเลือกลูกค้าในระบบ',
            ]);
            $customerId = $request->user_id;
        }

        $repairType = RepairType::findOrFail($request->repair_type_id);

        // Generate unique tracking code REP-YYYYXXX
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
            'device_model' => $request->device_model,
            'issue_description' => $request->issue_description,
            'status' => 'received',
            'created_at' => Carbon::now(),
        ]);

        $cost = $request->filled('estimated_cost') ? $request->estimated_cost : $repairType->base_price;
        $note = $request->filled('initial_note') ? $request->initial_note : 'รับเครื่องเข้าสู่ระบบตรวจเช็กเรียบร้อยแล้ว';

        RepairStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => 'received',
            'note' => $note,
            'cost' => $cost,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', "เปิดใบรับซ่อมรหัส {$ticket->tracking_code} เรียบร้อยแล้ว");
    }

    public function show($id)
    {
        $ticket = RepairTicket::with(['owner', 'repairType', 'statusLogs'])->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:received,repairing,completed,cancelled',
            'note' => 'required|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
        ], [
            'status.required' => 'กรุณาเลือกสถานะใหม่',
            'note.required' => 'กรุณาระบุรายละเอียดการดำเนินการจากช่าง',
        ]);

        $ticket = RepairTicket::findOrFail($id);
        $ticket->status = $request->status;
        $ticket->save();

        $cost = $request->filled('cost') ? $request->cost : $ticket->total_cost;

        RepairStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => $request->status,
            'note' => $request->note,
            'cost' => $cost,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', "อัปเดตสถานะเป็น '{$request->status}' และบันทึกประวัติเรียบร้อยแล้ว");
    }

    public function repairTypesIndex()
    {
        $repairTypes = RepairType::withCount('repairTickets')->orderBy('id', 'asc')->get();
        return view('admin.repair_types.index', compact('repairTypes'));
    }

    public function repairTypesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'กรุณาระบุชื่อประเภทงานซ่อม',
            'base_price.required' => 'กรุณาระบุราคาประเมินเริ่มต้น',
        ]);

        RepairType::create([
            'name' => $request->name,
            'base_price' => $request->base_price,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.repair_types.index')
            ->with('success', 'เพิ่มประเภทงานซ่อมใหม่สำเร็จ');
    }
}
