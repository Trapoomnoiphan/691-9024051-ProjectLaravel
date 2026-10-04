@extends('layouts.app')

@section('title', 'ติดตามสถานะงานซ่อมมือถือ')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 my-4">

    <!-- Search Hero Box -->
    <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-indigo-950 rounded-3xl p-8 text-white text-center shadow-2xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-white/10 backdrop-blur rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                🔍
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold">ติดตามสถานะงานซ่อมมือถือ</h1>
            <p class="text-sm text-indigo-200 mt-2">
                กรอกรหัสติดตามงานซ่อม (Tracking Code เช่น <span class="font-mono underline cursor-pointer" onclick="document.getElementById('trackQuery').value='REP-2026001'">REP-2026001</span>) หรือเบอร์โทรศัพท์ที่แจ้งซ่อมไว้
            </p>

            <form method="GET" action="{{ route('track') }}" class="mt-6 flex flex-col sm:flex-row gap-2">
                <input type="text" name="query" id="trackQuery" value="{{ $query ?? '' }}" required
                       placeholder="พิมพ์รหัส REP-2026001 หรือเบอร์โทร..."
                       class="w-full px-5 py-3.5 rounded-2xl text-gray-900 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-400 font-mono shadow-inner">
                <button type="submit" class="px-8 py-3.5 bg-amber-500 hover:bg-amber-400 text-gray-950 font-bold text-sm rounded-2xl shadow-lg transition whitespace-nowrap">
                    ตรวจสอบสถานะ
                </button>
            </form>
        </div>
    </div>

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center text-red-800">
            <div class="text-3xl mb-2">❌</div>
            <h3 class="font-bold text-base">{{ $errorMessage }}</h3>
            <p class="text-xs text-red-600 mt-1">กรุณาตรวจสอบความถูกต้องของรหัสติดตาม หรือโทรสอบถามทางร้าน</p>
        </div>
    @endif

    @if($ticket)
        <!-- Ticket Found Result Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Status Banner -->
            @php
                $statusConfig = [
                    'received' => ['color' => 'bg-blue-600', 'step' => 1, 'name' => 'รับเครื่องเข้าซ่อม', 'desc' => 'ช่างได้รับเครื่องเข้าสู่ระบบและรอคิวตรวจเช็ก'],
                    'repairing' => ['color' => 'bg-amber-500', 'step' => 2, 'name' => 'กำลังดำเนินการซ่อม', 'desc' => 'ช่างกำลังตรวจเช็กวงจร ถอดเปลี่ยนอะไหล่ หรือลงโปรแกรม'],
                    'completed' => ['color' => 'bg-emerald-600', 'step' => 3, 'name' => 'ซ่อมเสร็จสิ้น / พร้อมรับเครื่อง', 'desc' => 'การซ่อมและทดสอบเรียบร้อยแล้ว ท่านสามารถติดต่อรับเครื่องได้ทันที'],
                    'cancelled' => ['color' => 'bg-red-600', 'step' => 0, 'name' => 'ยกเลิกการซ่อม', 'desc' => 'งานซ่อมนี้ถูกยกเลิกแล้ว'],
                ];
                $sc = $statusConfig[$ticket->status] ?? $statusConfig['received'];
            @endphp

            <div class="{{ $sc['color'] }} p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="text-xs uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full font-bold">สถานะงานซ่อมปัจจุบัน</span>
                    <h2 class="text-2xl font-bold mt-2">{{ $sc['name'] }}</h2>
                    <p class="text-xs text-white/90 mt-1">{{ $sc['desc'] }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-white/80 block">รหัสติดตามงานซ่อม</span>
                    <span class="font-mono text-2xl font-bold tracking-wider">{{ $ticket->tracking_code }}</span>
                </div>
            </div>

            <!-- Progress Step Indicator -->
            @if($ticket->status !== 'cancelled')
                <div class="px-6 py-6 bg-gray-50 border-b border-gray-100">
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white shadow {{ $sc['step'] >= 1 ? 'bg-blue-600' : 'bg-gray-300' }}">1</div>
                            <span class="mt-2 font-medium {{ $sc['step'] >= 1 ? 'text-blue-900 font-bold' : 'text-gray-400' }}">รับเครื่องเข้าสู่ระบบ</span>
                        </div>
                        <!-- Step 2 -->
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white shadow {{ $sc['step'] >= 2 ? 'bg-amber-500' : 'bg-gray-300' }}">2</div>
                            <span class="mt-2 font-medium {{ $sc['step'] >= 2 ? 'text-amber-900 font-bold' : 'text-gray-400' }}">กำลังซ่อมแซม</span>
                        </div>
                        <!-- Step 3 -->
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white shadow {{ $sc['step'] >= 3 ? 'bg-emerald-600' : 'bg-gray-300' }}">3</div>
                            <span class="mt-2 font-medium {{ $sc['step'] >= 3 ? 'text-emerald-900 font-bold' : 'text-gray-400' }}">ซ่อมเสร็จ / พร้อมรับ</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Body Details -->
            <div class="p-6 space-y-6">
                <!-- Device & Cost Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-xs text-gray-400 block">อุปกรณ์ที่ส่งซ่อม:</span>
                        <span class="font-bold text-base text-gray-900">{{ $ticket->device_model }}</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-xs text-gray-400 block">ประเภทบริการ:</span>
                        <span class="font-bold text-base text-indigo-700">{{ $ticket->repairType->name ?? '-' }}</span>
                    </div>

                    <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100 sm:col-span-2 md:col-span-1">
                        <span class="text-xs text-emerald-600 block">ค่าบริการ / อะไหล่ทั้งหมด:</span>
                        <span class="font-bold text-2xl text-emerald-700">฿{{ number_format($ticket->total_cost, 2) }}</span>
                    </div>
                </div>

                <!-- Issue Description -->
                <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100">
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wide block mb-1">รายละเอียดอาการเสียที่แจ้งไว้:</span>
                    <p class="text-xs text-gray-800 leading-relaxed">{{ $ticket->issue_description }}</p>
                </div>

                <!-- Timeline Logs -->
                <div>
                    <h3 class="font-bold text-sm text-gray-900 mb-4 flex items-center space-x-2">
                        <span>📜</span>
                        <span>ประวัติความคืบหน้าและการปฏิบัติงานของช่าง</span>
                    </h3>

                    <div class="relative border-l-2 border-indigo-200 ml-3 space-y-6">
                        @foreach($ticket->statusLogs as $log)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-indigo-600 border-2 border-white shadow-sm"></div>
                                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200/70">
                                    <div class="flex justify-between items-center text-xs text-gray-500 mb-1">
                                        <span class="font-bold text-gray-900 uppercase font-mono">{{ $log->status }}</span>
                                        <span>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-700">{{ $log->note ?? '-' }}</p>
                                    @if($log->cost !== null)
                                        <div class="mt-1 text-[11px] font-semibold text-emerald-700">
                                            ค่าใช้จ่าย: ฿{{ number_format($log->cost, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
@endsection
