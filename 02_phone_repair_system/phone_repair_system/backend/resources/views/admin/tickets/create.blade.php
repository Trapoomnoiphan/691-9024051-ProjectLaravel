@extends('layouts.app')

@section('title', 'เปิดใบรับซ่อมใหม่')

@section('content')
<div class="max-w-3xl mx-auto my-6">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 p-6 text-white flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold">เปิดใบรับซ่อม / รับเครื่องเข้าสู่ระบบ</h1>
                <p class="text-xs text-indigo-200 mt-1">บันทึกข้อมูลเครื่องลูกค้า ประเภทอาการเสีย และราคาประเมินเบื้องต้น</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs px-3 py-1.5 bg-indigo-700 hover:bg-indigo-600 rounded-lg text-white transition">
                ← กลับแดชบอร์ด
            </a>
        </div>

        <form method="POST" action="{{ route('admin.tickets.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Section 1: ข้อมูลลูกค้า -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-indigo-950 uppercase tracking-wider border-b pb-2 flex items-center space-x-2">
                    <span>👤</span>
                    <span>1. ข้อมูลลูกค้า</span>
                </h2>

                <div class="flex items-center space-x-4 mb-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="customer_type" value="existing" checked onchange="toggleCustomerType(this.value)" class="text-indigo-600">
                        <span class="ml-2 text-sm font-medium text-gray-700">เลือกลูกค้าเดิมในระบบ</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="customer_type" value="new" onchange="toggleCustomerType(this.value)" class="text-indigo-600">
                        <span class="ml-2 text-sm font-medium text-gray-700">ลงทะเบียนลูกค้าใหม่ทันที</span>
                    </label>
                </div>

                <!-- Existing Customer Dropdown -->
                <div id="existingCustomerBox">
                    <label for="user_id" class="block text-xs font-semibold text-gray-600 mb-1">ค้นหาและเลือกลูกค้า</label>
                    <select name="user_id" id="user_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                        <option value="">-- กรุณาเลือกลูกค้า --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('user_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} (เบอร์โทร: {{ $c->phone }} | อีเมล: {{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- New Customer Inputs -->
                <div id="newCustomerBox" class="hidden space-y-3 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">ชื่อ-นามสกุลลูกค้า *</label>
                            <input type="text" name="new_name" value="{{ old('new_name') }}" placeholder="เช่น นายวิทยา สุขใจ" class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">เบอร์โทรศัพท์ *</label>
                            <input type="tel" name="new_phone" value="{{ old('new_phone') }}" placeholder="เช่น 0819998877" class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">อีเมลลูกค้า *</label>
                        <input type="email" name="new_email" value="{{ old('new_email') }}" placeholder="เช่น client@example.com" class="w-full px-3 py-2 rounded-lg border text-sm">
                    </div>
                </div>
            </div>

            <!-- Section 2: ข้อมูลเครื่องและประเภทงานซ่อม -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-indigo-950 uppercase tracking-wider border-b pb-2 flex items-center space-x-2">
                    <span>📱</span>
                    <span>2. ข้อมูลเครื่องและรายการซ่อม</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="device_model" class="block text-xs font-semibold text-gray-600 mb-1">ยี่ห้อและรุ่นของอุปกรณ์ *</label>
                        <input type="text" name="device_model" id="device_model" value="{{ old('device_model') }}" required
                               placeholder="เช่น iPhone 14 Pro, Galaxy S24 Ultra"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <div>
                        <label for="repair_type_id" class="block text-xs font-semibold text-gray-600 mb-1">ประเภทงานซ่อมและบริการ *</label>
                        <select name="repair_type_id" id="repair_type_id" required onchange="updateBasePrice(this)"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                            <option value="">-- เลือกประเภทการซ่อม --</option>
                            @foreach($repairTypes as $type)
                                <option value="{{ $type->id }}" data-price="{{ $type->base_price }}" {{ old('repair_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} (ราคาเริ่มต้น ฿{{ number_format($type->base_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="issue_description" class="block text-xs font-semibold text-gray-600 mb-1">รายละเอียดอาการเสีย *</label>
                    <textarea name="issue_description" id="issue_description" rows="3" required
                              placeholder="ระบุอาการผิดปกติ เช่น ตกน้ำ เปิดไม่ติด ชาร์จไม่เข้า หรือสัมผัสหน้าจอเพี้ยน..."
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">{{ old('issue_description') }}</textarea>
                </div>
            </div>

            <!-- Section 3: ราคาประเมินและบันทึกช่าง -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-indigo-950 uppercase tracking-wider border-b pb-2 flex items-center space-x-2">
                    <span>💵</span>
                    <span>3. ราคาประเมินและบันทึกตรวจรับ</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="estimated_cost" class="block text-xs font-semibold text-gray-600 mb-1">ราคาประเมินเริ่มต้น (บาท)</label>
                        <input type="number" step="0.01" name="estimated_cost" id="estimated_cost" value="{{ old('estimated_cost') }}"
                               placeholder="1500.00"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <div>
                        <label for="initial_note" class="block text-xs font-semibold text-gray-600 mb-1">บันทึกสภาพเครื่องตอนรับ</label>
                        <input type="text" name="initial_note" id="initial_note" value="{{ old('initial_note', 'รับเครื่องเข้าสู่ระบบตรวจเช็กเรียบร้อย') }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm transition">
                    ยกเลิก
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm shadow-md transition flex items-center space-x-2">
                    <span>💾</span>
                    <span>บันทึกและเปิดใบรับซ่อม</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCustomerType(type) {
        if (type === 'new') {
            document.getElementById('existingCustomerBox').classList.add('hidden');
            document.getElementById('newCustomerBox').classList.remove('hidden');
        } else {
            document.getElementById('existingCustomerBox').classList.remove('hidden');
            document.getElementById('newCustomerBox').classList.add('hidden');
        }
    }

    function updateBasePrice(select) {
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price');
        if (price) {
            document.getElementById('estimated_cost').value = price;
        }
    }
</script>
@endsection
