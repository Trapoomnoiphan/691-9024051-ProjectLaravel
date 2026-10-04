@extends('layouts.app')

@section('title', 'รายละเอียดใบรับซ่อม ' . $ticket->tracking_code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-indigo-950 font-mono">{{ $ticket->tracking_code }}</h1>
                @php
                    $badges = [
                        'received' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'text' => '📥 รับเครื่องเข้าซ่อม'],
                        'repairing' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'text' => '🔧 กำลังดำเนินการซ่อม'],
                        'completed' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'text' => '✅ ซ่อมเสร็จสิ้น'],
                        'cancelled' => ['bg' => 'bg-red-100 text-red-800 border-red-200', 'text' => '❌ ยกเลิกการซ่อม'],
                    ];
                    $b = $badges[$ticket->status] ?? ['bg' => 'bg-gray-100 text-gray-800 border-gray-200', 'text' => $ticket->status];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $b['bg'] }}">
                    {{ $b['text'] }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">รับเครื่องเมื่อ: {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium transition">
                ← กลับหน้าแดชบอร์ด
            </a>
            <a href="{{ route('track', ['query' => $ticket->tracking_code]) }}" target="_blank" class="px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-medium transition">
                👁️ มุมมองลูกค้า
            </a>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Col 1: Customer Details -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-3">
            <h2 class="font-bold text-sm text-gray-800 border-b pb-2 flex items-center space-x-2">
                <span>👤</span>
                <span>ข้อมูลลูกค้า</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-gray-400 block">ชื่อลูกค้า:</span>
                    <span class="font-medium text-gray-900 text-sm">{{ $ticket->owner->name ?? 'ไม่ระบุ' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">เบอร์โทรศัพท์:</span>
                    <span class="font-mono text-gray-900">{{ $ticket->owner->phone ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">อีเมล:</span>
                    <span class="text-gray-900">{{ $ticket->owner->email ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Col 2: Device & Repair Info -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-3">
            <h2 class="font-bold text-sm text-gray-800 border-b pb-2 flex items-center space-x-2">
                <span>📱</span>
                <span>ข้อมูลเครื่องและอาการเสีย</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-gray-400 block">ยี่ห้อ / รุ่น:</span>
                    <span class="font-medium text-gray-900 text-sm">{{ $ticket->device_model }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">ประเภทงานซ่อม:</span>
                    <span class="font-medium text-indigo-700">{{ $ticket->repairType->name ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">อาการเสียที่ระบุ:</span>
                    <p class="text-gray-700 mt-0.5 bg-gray-50 p-2 rounded-lg border border-gray-100">{{ $ticket->issue_description }}</p>
                </div>
            </div>
        </div>

        <!-- Col 3: Cost Summary -->
        <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 p-5 rounded-2xl shadow-md text-white space-y-3">
            <h2 class="font-bold text-sm text-indigo-200 border-b border-indigo-800 pb-2 flex items-center space-x-2">
                <span>💰</span>
                <span>สรุปค่าบริการ</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div>
                    <span class="text-indigo-300 block">ราคาเริ่มต้นของงานซ่อม:</span>
                    <span class="font-semibold text-sm">฿{{ number_format($ticket->repairType->base_price ?? 0, 2) }}</span>
                </div>
                <div class="pt-2 border-t border-indigo-800/80">
                    <span class="text-indigo-300 block">ค่าบริการ / ค่าอะไหล่จริงปัจจุบัน:</span>
                    <span class="font-bold text-2xl text-emerald-400">฿{{ number_format($ticket->total_cost, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status & Work Log Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-indigo-50/70 px-6 py-4 border-b border-indigo-100 flex items-center justify-between">
            <h2 class="font-bold text-indigo-950 text-sm flex items-center space-x-2">
                <span>🛠️</span>
                <span>อัปเดตสถานะงานซ่อม & บันทึกการปฏิบัติงานของช่าง</span>
            </h2>
            <span class="text-xs text-indigo-600 font-medium">บันทึกจะถูกเพิ่มในไทม์ไลน์สถานะทันที</span>
        </div>

        <form method="POST" action="{{ route('admin.tickets.update_status', $ticket->id) }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">เปลี่ยนสถานะเป็น *</label>
                    <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                        <option value="received" {{ $ticket->status === 'received' ? 'selected' : '' }}>📥 1. รับเครื่องเข้าซ่อม (Received)</option>
                        <option value="repairing" {{ $ticket->status === 'repairing' ? 'selected' : '' }}>🔧 2. กำลังดำเนินการซ่อม (Repairing)</option>
                        <option value="completed" {{ $ticket->status === 'completed' ? 'selected' : '' }}>✅ 3. ซ่อมเสร็จสิ้น / พร้อมส่งมอบ (Completed)</option>
                        <option value="cancelled" {{ $ticket->status === 'cancelled' ? 'selected' : '' }}>❌ 4. ยกเลิกการซ่อม (Cancelled)</option>
                    </select>
                </div>

                <div>
                    <label for="cost" class="block text-xs font-semibold text-gray-700 mb-1">อัปเดตราคา/ค่าบริการจริง (บาท)</label>
                    <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost', $ticket->total_cost) }}"
                           placeholder="เช่น 1500.00"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                </div>
            </div>

            <div>
                <label for="note" class="block text-xs font-semibold text-gray-700 mb-1">บันทึกรายละเอียดการซ่อม / ขั้นตอนการแก้ปัญหา *</label>
                <textarea name="note" id="note" rows="3" required
                          placeholder="เช่น ถอดหน้าจอเก่าออกและทำความสะอาดคราบกาวเดิม ติดตั้งหน้าจอชุดใหม่พร้อมซีลกันน้ำและทดสอบระบบสัมผัส..."
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">{{ old('note') }}</textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-md transition flex items-center space-x-2">
                    <span>💾</span>
                    <span>บันทึกความคืบหน้า</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Timeline of Status Logs -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-800 text-base mb-6 flex items-center space-x-2">
            <span>📜</span>
            <span>ประวัติไทม์ไลน์ความคืบหน้า (Status Logs Timeline)</span>
        </h2>

        <div class="relative border-l-2 border-indigo-200 ml-4 space-y-6">
            @foreach($ticket->statusLogs as $log)
                <div class="relative pl-6">
                    <!-- Dot -->
                    @php
                        $colors = [
                            'received' => 'bg-blue-600',
                            'repairing' => 'bg-amber-500',
                            'completed' => 'bg-emerald-600',
                            'cancelled' => 'bg-red-600',
                        ];
                        $dotColor = $colors[$log->status] ?? 'bg-indigo-600';
                    @endphp
                    <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full {{ $dotColor }} border-2 border-white shadow-sm"></div>

                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/70">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-1 mb-2">
                            <span class="font-bold text-sm text-gray-900">
                                สถานะ: <span class="uppercase font-mono text-indigo-700">{{ $log->status }}</span>
                            </span>
                            <span class="text-xs text-gray-500">
                                🕒 {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-700 leading-relaxed">{{ $log->note ?? '-' }}</p>

                        @if($log->cost !== null)
                            <div class="mt-2 text-xs font-semibold text-emerald-700">
                                ค่าบริการในขั้นตอนนี้: ฿{{ number_format($log->cost, 2) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
