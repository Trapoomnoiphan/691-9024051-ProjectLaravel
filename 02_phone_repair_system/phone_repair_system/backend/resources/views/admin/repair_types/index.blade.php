@extends('layouts.app')

@section('title', 'จัดการประเภทงานซ่อม')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-indigo-950">⚙️ จัดการประเภทงานซ่อมและบริการ</h1>
            <p class="text-xs text-gray-500 mt-1">กำหนดประเภทงานซ่อมและราคาประเมินมาตรฐานของร้าน</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
            ← กลับแดชบอร์ด
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form: Add Repair Type -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit">
            <h2 class="font-bold text-sm text-indigo-950 mb-4 flex items-center space-x-2">
                <span>➕</span>
                <span>เพิ่มประเภทงานซ่อมใหม่</span>
            </h2>

            <form method="POST" action="{{ route('admin.repair_types.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">ชื่อประเภทงานซ่อม *</label>
                    <input type="text" name="name" id="name" required placeholder="เช่น ซ่อมกล้องหลัง / เปลี่ยนเลนส์"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label for="base_price" class="block text-xs font-semibold text-gray-700 mb-1">ราคาประเมินเริ่มต้น (บาท) *</label>
                    <input type="number" step="0.01" name="base_price" id="base_price" required placeholder="เช่น 1200.00"
                           class="w-full px-3 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md">
                    บันทึกประเภทงานซ่อม
                </button>
            </form>
        </div>

        <!-- Table: List of Repair Types -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-sm text-gray-800">รายการประเภทงานซ่อมที่มีในระบบ ({{ $repairTypes->count() }})</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">ประเภทงานซ่อม</th>
                            <th class="px-6 py-3">ราคาเริ่มต้น</th>
                            <th class="px-6 py-3">จำนวนเครื่องที่ซ่อม</th>
                            <th class="px-6 py-3">วันที่สร้าง</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($repairTypes as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3.5 text-xs text-gray-400 font-mono">{{ $t->id }}</td>
                                <td class="px-6 py-3.5 font-medium text-gray-900">{{ $t->name }}</td>
                                <td class="px-6 py-3.5 font-bold text-emerald-700">฿{{ number_format($t->base_price, 2) }}</td>
                                <td class="px-6 py-3.5 text-xs text-gray-600">{{ $t->repair_tickets_count ?? 0 }} รายการ</td>
                                <td class="px-6 py-3.5 text-xs text-gray-400">{{ $t->created_at ? $t->created_at->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
