@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 p-6 text-white text-center">
            <div class="w-14 h-14 bg-indigo-700/80 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                🔐
            </div>
            <h1 class="text-2xl font-bold">เข้าสู่ระบบ</h1>
            <p class="text-xs text-indigo-200 mt-1">กรุณากรอกอีเมลและรหัสผ่านเพื่อเข้าสู่ระบบงานซ่อม</p>
        </div>

        <div class="p-6">
            <!-- Quick Fill Demo Buttons -->
            <div class="mb-6 p-4 bg-indigo-50/70 rounded-xl border border-indigo-100">
                <p class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">⚡ บัญชีทดสอบระบบ (คลิกเพื่อเติมข้อมูลทันที):</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button type="button" onclick="fillLogin('admin@repairsystem.com', 'admin1234')" class="text-left px-3 py-2 bg-white hover:bg-amber-50 border border-amber-300 rounded-lg text-xs transition shadow-sm">
                        <div class="font-bold text-amber-800">👑 Admin (ช่างซ่อม)</div>
                        <div class="text-gray-500 font-mono text-[11px]">admin@repairsystem.com</div>
                    </button>
                    <button type="button" onclick="fillLogin('user@repairsystem.com', 'user1234')" class="text-left px-3 py-2 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg text-xs transition shadow-sm">
                        <div class="font-bold text-blue-800">👤 User (ลูกค้า)</div>
                        <div class="text-gray-500 font-mono text-[11px]">user@repairsystem.com</div>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">อีเมลผู้ใช้งาน</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                           placeholder="เช่น admin@repairsystem.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 text-sm transition"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-xs">จดจำการเข้าสู่ระบบ</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-md hover:shadow-lg transition">
                    เข้าสู่ระบบ
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-gray-500 border-t border-gray-100 pt-4">
                ยังไม่มีบัญชีผู้ใช้งานใช่หรือไม่? 
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline">สมัครสมาชิกใหม่ที่นี่</a>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
