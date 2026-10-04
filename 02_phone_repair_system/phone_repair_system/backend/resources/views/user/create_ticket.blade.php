@extends('layouts.app')

@section('title', 'ยื่นคำขอแจ้งซ่อมมือถือ')

@section('content')
<div class="max-w-2xl mx-auto my-6">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 p-6 text-white text-center">
            <div class="w-14 h-14 bg-indigo-700/80 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                📝
            </div>
            <h1 class="text-2xl font-bold">ยื่นคำขอแจ้งซ่อมมือถือออนไลน์</h1>
            <p class="text-xs text-indigo-200 mt-1">กรอกข้อมูลเครื่องและอาการเสียเพื่อรับรหัสติดตามงานซ่อมทันที</p>
        </div>

        <form method="POST" action="{{ route('user.tickets.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="repair_type_id" class="block text-xs font-semibold text-gray-700 mb-1">เลือกประเภทการซ่อม / บริการ *</label>
                <select name="repair_type_id" id="repair_type_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                    <option value="">-- กรุณาเลือกประเภทงานซ่อม --</option>
                    @foreach($repairTypes as $type)
                        <option value="{{ $type->id }}" {{ old('repair_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} (ราคาเริ่มต้น ฿{{ number_format($type->base_price, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="device_model" class="block text-xs font-semibold text-gray-700 mb-1">ยี่ห้อและรุ่นของอุปกรณ์ *</label>
                <input type="text" name="device_model" id="device_model" value="{{ old('device_model') }}" required
                       placeholder="เช่น iPhone 13 Pro 128GB สีดำ หรือ Samsung Galaxy S23"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
            </div>

            <div>
                <label for="issue_description" class="block text-xs font-semibold text-gray-700 mb-1">รายละเอียดอาการเสีย *</label>
                <textarea name="issue_description" id="issue_description" rows="4" required
                          placeholder="อธิบายอาการผิดปกติ เช่น จอแตกเป็นเส้น ทัชไม่ได้ แบตเตอรี่หมดเร็ว ชาร์จไฟไม่เข้า หรือเปิดเครื่องไม่ติด..."
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">{{ old('issue_description') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('user.my_tickets') }}" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs transition">
                    ยกเลิก
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    ยื่นคำขอแจ้งซ่อมทันที
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
