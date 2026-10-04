@extends('layouts.app')

@section('title', 'งานซ่อมของฉัน')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-950">📋 งานซ่อมของฉัน</h1>
            <p class="text-xs text-gray-500 mt-1">ติดตามสถานะและความคืบหน้าของเครื่องที่คุณส่งซ่อมไว้ทั้งหมด</p>
        </div>
        <a href="{{ route('user.tickets.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center space-x-1.5">
            <span>➕</span>
            <span>ยื่นแจ้งซ่อมใหม่</span>
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
            <div class="text-4xl mb-3">📱</div>
            <h3 class="font-bold text-base text-gray-800">ยังไม่มีรายการส่งซ่อมในระบบ</h3>
            <p class="text-xs text-gray-500 mt-1">หากต้องการส่งเครื่องตรวจเช็กหรือเปลี่ยนอะไหล่ สามารถยื่นคำขอแจ้งซ่อมได้ทันที</p>
            <div class="mt-6">
                <a href="{{ route('user.tickets.create') }}" class="inline-block px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                    ยื่นคำขอแจ้งซ่อมออนไลน์
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tickets as $ticket)
                @php
                    $badges = [
                        'received' => ['bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'text' => '📥 รับเครื่องแล้ว'],
                        'repairing' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'text' => '🔧 กำลังดำเนินการซ่อม'],
                        'completed' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'text' => '✅ ซ่อมเสร็จสิ้น'],
                        'cancelled' => ['bg' => 'bg-red-100 text-red-800 border-red-200', 'text' => '❌ ยกเลิก'],
                    ];
                    $b = $badges[$ticket->status] ?? ['bg' => 'bg-gray-100 text-gray-800 border-gray-200', 'text' => $ticket->status];
                @endphp

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-mono font-bold text-indigo-700 text-base">{{ $ticket->tracking_code }}</span>
                                <div class="text-xs text-gray-400 mt-0.5">ส่งเรื่องเมื่อ: {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $b['bg'] }}">
                                {{ $b['text'] }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-1 text-xs">
                            <div class="font-bold text-gray-900 text-sm">{{ $ticket->device_model }}</div>
                            <div class="text-indigo-600 font-medium">ประเภท: {{ $ticket->repairType->name ?? '-' }}</div>
                            <p class="text-gray-600 line-clamp-2 bg-gray-50 p-2 rounded-lg border border-gray-100 mt-2">{{ $ticket->issue_description }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                        <div>
                            <span class="text-[11px] text-gray-400 block">ค่าบริการรวม:</span>
                            <span class="font-bold text-emerald-700 text-base">฿{{ number_format($ticket->total_cost, 2) }}</span>
                        </div>
                        <a href="{{ route('track', ['query' => $ticket->tracking_code]) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium transition">
                            ดูไทม์ไลน์ความคืบหน้า →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
