@extends('layouts.app')

@section('title', 'แดชบอร์ดงานซ่อม - ช่างซ่อม/Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Action Banner -->
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 bg-amber-500 text-black text-xs font-bold rounded-full uppercase">Admin Technician</span>
                <span class="text-xs text-indigo-200">ระบบบริหารจัดการและติดตามงานซ่อม</span>
            </div>
            <h1 class="text-2xl font-bold mt-1">แผงควบคุมช่างซ่อมมือถือ (Repair Dashboard)</h1>
            <p class="text-xs text-indigo-200 mt-1">ตรวจสอบสถานะ อัปเดตความคืบหน้า ประเมินราคา และออกใบรับซ่อม</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tickets.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-xl shadow-md transition flex items-center space-x-1.5">
                <span>➕</span>
                <span>เปิดใบรับเครื่องใหม่</span>
            </a>
            <a href="{{ route('admin.repair_types.index') }}" class="px-4 py-2.5 bg-indigo-700 hover:bg-indigo-600 text-white text-sm font-medium rounded-xl transition flex items-center space-x-1.5">
                <span>⚙️</span>
                <span>ประเภทงานซ่อม</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500">งานซ่อมทั้งหมด</span>
                <span class="text-lg">📁</span>
            </div>
            <div class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-gray-400 mt-1">เครื่องทั้งหมดในระบบ</div>
        </div>

        <!-- Received -->
        <a href="{{ route('admin.dashboard', ['status' => 'received']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-blue-100 hover:border-blue-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-blue-700">รับเครื่องแล้ว</span>
                <span class="text-lg">📥</span>
            </div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['received'] }}</div>
            <div class="text-[11px] text-blue-500 mt-1">รอเริ่มตรวจเช็ก/ซ่อม</div>
        </a>

        <!-- Repairing -->
        <a href="{{ route('admin.dashboard', ['status' => 'repairing']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-amber-100 hover:border-amber-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-700">กำลังซ่อม</span>
                <span class="text-lg">🔧</span>
            </div>
            <div class="text-2xl font-bold text-amber-600 mt-2">{{ $stats['repairing'] }}</div>
            <div class="text-[11px] text-amber-500 mt-1">ช่างกำลังดำเนินการ</div>
        </a>

        <!-- Completed -->
        <a href="{{ route('admin.dashboard', ['status' => 'completed']) }}" class="bg-white rounded-xl p-4 shadow-sm border border-emerald-100 hover:border-emerald-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-700">ซ่อมเสร็จสิ้น</span>
                <span class="text-lg">✅</span>
            </div>
            <div class="text-2xl font-bold text-emerald-600 mt-2">{{ $stats['completed'] }}</div>
            <div class="text-[11px] text-emerald-500 mt-1">พร้อมส่งมอบลูกค้า</div>
        </a>

        <!-- Revenue -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-purple-100 col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-purple-700">ยอดค่าบริการรวม</span>
                <span class="text-lg">💰</span>
            </div>
            <div class="text-2xl font-bold text-purple-700 mt-2">฿{{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="text-[11px] text-purple-500 mt-1">จากงานซ่อมที่สำเร็จ</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <span class="text-xs font-bold text-gray-500 uppercase mr-1">สถานะ:</span>
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ !request()->filled('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    ทั้งหมด ({{ $stats['total'] }})
                </a>
                <a href="{{ route('admin.dashboard', ['status' => 'received']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ request('status') === 'received' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                    รับเครื่อง ({{ $stats['received'] }})
                </a>
                <a href="{{ route('admin.dashboard', ['status' => 'repairing']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ request('status') === 'repairing' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    กำลังซ่อม ({{ $stats['repairing'] }})
                </a>
                <a href="{{ route('admin.dashboard', ['status' => 'completed']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ request('status') === 'completed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    ซ่อมเสร็จแล้ว ({{ $stats['completed'] }})
                </a>
                <a href="{{ route('admin.dashboard', ['status' => 'cancelled']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ request('status') === 'cancelled' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                    ยกเลิก ({{ $stats['cancelled'] }})
                </a>
            </div>

            <!-- Search Field -->
            <div class="flex items-center gap-2 w-full md:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหารหัส REP, ลูกค้า, เบอร์โทร..."
                       class="w-full px-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600">
                <button type="submit" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-xl transition">
                    ค้นหา
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-500 hover:text-red-600">รีเซ็ต</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-bold text-gray-800 text-base">รายการใบแจ้งซ่อมและสถานะงาน ({{ $tickets->count() }} รายการ)</h2>
        </div>

        @if($tickets->isEmpty())
            <div class="text-center py-12 text-gray-400">
                <div class="text-4xl mb-2">🔍</div>
                <p class="text-sm">ไม่พบรายการงานซ่อมตามเงื่อนไขที่ค้นหา</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3.5">รหัสติดตาม</th>
                            <th class="px-6 py-3.5">วันที่รับเครื่อง</th>
                            <th class="px-6 py-3.5">ลูกค้า</th>
                            <th class="px-6 py-3.5">อุปกรณ์ / รุ่น</th>
                            <th class="px-6 py-3.5">ประเภทงานซ่อม</th>
                            <th class="px-6 py-3.5">สถานะ</th>
                            <th class="px-6 py-3.5">ค่าบริการ</th>
                            <th class="px-6 py-3.5 text-right">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-indigo-50/40 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-indigo-700">{{ $ticket->tracking_code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $ticket->owner->name ?? 'ไม่ระบุ' }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $ticket->owner->phone ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $ticket->device_model }}</div>
                                    <div class="text-xs text-gray-400 line-clamp-1">{{ $ticket->issue_description }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-gray-700">{{ $ticket->repairType->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badges = [
                                            'received' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'text' => '📥 รับเครื่องเข้าซ่อม'],
                                            'repairing' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'text' => '🔧 กำลังดำเนินการซ่อม'],
                                            'completed' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'text' => '✅ ซ่อมเสร็จสิ้น'],
                                            'cancelled' => ['bg' => 'bg-red-100 text-red-800 border-red-200', 'text' => '❌ ยกเลิกการซ่อม'],
                                        ];
                                        $b = $badges[$ticket->status] ?? ['bg' => 'bg-gray-100 text-gray-800 border-gray-200', 'text' => $ticket->status];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $b['bg'] }}">
                                        {{ $b['text'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                    ฿{{ number_format($ticket->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="inline-block px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-medium shadow-sm transition">
                                        อัปเดต / ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
