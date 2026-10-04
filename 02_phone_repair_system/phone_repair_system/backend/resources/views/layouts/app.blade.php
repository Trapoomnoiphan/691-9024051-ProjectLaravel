<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบติดตามสถานะร้านซ่อมมือถือ') - PhoneFix Pro</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <!-- Navigation Header -->
    <nav class="bg-indigo-950 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Logo & Title -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-md group-hover:scale-105 transition transform">
                            📱
                        </div>
                        <div>
                            <span class="font-bold text-lg tracking-wide text-white group-hover:text-indigo-200 transition">PhoneFix Pro</span>
                            <span class="block text-xs text-indigo-300 font-light">ระบบติดตามสถานะร้านซ่อมมือถือ</span>
                        </div>
                    </a>
                </div>

                <!-- Right: Navigation Links -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('track') }}" class="px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-900 transition flex items-center space-x-1 {{ request()->routeIs('track') ? 'bg-indigo-900 text-indigo-200' : 'text-gray-200' }}">
                        <span>🔍</span>
                        <span>ติดตามสถานะ</span>
                    </a>

                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-900 transition flex items-center space-x-1 {{ request()->routeIs('admin.dashboard*') ? 'bg-indigo-900 text-indigo-200' : 'text-gray-200' }}">
                                <span>📊</span>
                                <span>แดชบอร์ดช่าง</span>
                            </a>
                            <a href="{{ route('admin.tickets.create') }}" class="px-3 py-2 rounded-lg text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center space-x-1 shadow-sm">
                                <span>➕</span>
                                <span class="hidden sm:inline">เปิดใบรับซ่อม</span>
                            </a>
                            <a href="{{ route('admin.repair_types.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-900 text-gray-200 transition">
                                <span>⚙️ ประเภทงานซ่อม</span>
                            </a>
                        @else
                            <a href="{{ route('user.my_tickets') }}" class="px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-900 transition flex items-center space-x-1 {{ request()->routeIs('user.my_tickets') ? 'bg-indigo-900 text-indigo-200' : 'text-gray-200' }}">
                                <span>📋</span>
                                <span>งานซ่อมของฉัน</span>
                            </a>
                            <a href="{{ route('user.tickets.create') }}" class="px-3 py-2 rounded-lg text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white transition flex items-center space-x-1 shadow-sm">
                                <span>📝</span>
                                <span class="hidden sm:inline">แจ้งซ่อมใหม่</span>
                            </a>
                        @endif

                        <!-- User Profile Badge & Logout -->
                        <div class="flex items-center pl-2 border-l border-indigo-800 space-x-2">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ Auth::user()->role === 'admin' ? 'bg-amber-500 text-black' : 'bg-indigo-800 text-indigo-100' }}">
                                {{ Auth::user()->role === 'admin' ? '👨‍🔧 ช่าง/Admin' : '👤 ลูกค้า' }}: {{ Str::limit(Auth::user()->name, 12) }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-red-300 hover:text-red-100 px-2 py-1 rounded hover:bg-red-900/50 transition">
                                    ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-200 hover:bg-indigo-900 transition">
                            เข้าสู่ระบบ
                        </a>
                        <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-sm">
                            สมัครสมาชิก
                        </a>
                    @endauth

                    <!-- Portal Cross-Links -->
                    <div class="flex items-center space-x-2 pl-2 border-l border-indigo-800">
                        <a href="/flutter" target="_blank" class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-sky-600 hover:bg-sky-500 text-white transition flex items-center space-x-1" title="เปิดหน้าเว็บฝั่ง Flutter Web">
                            <span>📱</span>
                            <span class="hidden md:inline">Flutter Web</span>
                        </a>
                        <a href="/api-docs" target="_blank" class="px-2.5 py-1.5 rounded-lg text-xs font-mono font-medium bg-gray-800 hover:bg-gray-700 text-amber-300 transition" title="เปิดดู Raw JSON API">
                            { } API
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-lg shadow-sm mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-lg shadow-sm mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 rounded-r-lg shadow-sm mb-4">
                <div class="font-bold mb-1">เกิดข้อผิดพลาดในการกรอกข้อมูล:</div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12 py-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-medium text-gray-700">ระบบติดตามสถานะร้านซ่อมมือถือ (Mobile Phone Repair Status Tracking System)</p>
            <p class="mt-1">สถาปัตยกรรม Laravel 11.x + MySQL + Sanctum API + Flutter Web Material Client</p>
            <div class="mt-2 flex justify-center space-x-4">
                <a href="{{ route('track') }}" class="hover:text-indigo-600">ติดตามสถานะงานซ่อม</a>
                <span>•</span>
                <a href="/flutter" target="_blank" class="hover:text-indigo-600">หน้าเว็บ Flutter Web</a>
                <span>•</span>
                <a href="/api-docs" target="_blank" class="hover:text-indigo-600">Raw JSON API Explorer</a>
            </div>
        </div>
    </footer>

</body>
</html>
