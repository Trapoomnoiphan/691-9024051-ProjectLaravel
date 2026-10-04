@extends('layouts.app')

@section('title', 'สมัครสมาชิก')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 p-6 text-white text-center">
            <div class="w-14 h-14 bg-indigo-700/80 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                📝
            </div>
            <h1 class="text-2xl font-bold">สมัครสมาชิกใหม่</h1>
            <p class="text-xs text-indigo-200 mt-1">ลงทะเบียนบัญชีลูกค้าเพื่อติดตามและแจ้งงานซ่อมออนไลน์</p>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อ - นามสกุล</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                           placeholder="เช่น วีรชัย มีทรัพย์">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์มือถือ</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                           placeholder="เช่น 0891234567">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                           placeholder="เช่น customer@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน (ขั้นต่ำ 6 ตัวอักษร)</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                           placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่านอีกครั้ง</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm"
                           placeholder="••••••••">
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-md hover:shadow-lg transition">
                    ลงทะเบียนเข้าสู่ระบบ
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-500 border-t border-gray-100 pt-4">
                มีบัญชีอยู่แล้ว? 
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">เข้าสู่ระบบที่นี่</a>
            </div>
        </div>
    </div>
</div>
@endsection
