<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AdminTicketController;
use App\Http\Controllers\Web\UserTicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home & Public Tracking
Route::get('/', [UserTicketController::class, 'track'])->name('home');
Route::get('/track', [UserTicketController::class, 'track'])->name('track');

// Authentication Routes (Laravel Web Blade)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer (User) Protected Routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/my-tickets', [UserTicketController::class, 'myTickets'])->name('user.my_tickets');
    Route::get('/tickets/create', [UserTicketController::class, 'create'])->name('user.tickets.create');
    Route::post('/tickets', [UserTicketController::class, 'store'])->name('user.tickets.store');
});

// Admin Protected Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminTicketController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets/create', [AdminTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [AdminTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.update_status');
    Route::get('/repair-types', [AdminTicketController::class, 'repairTypesIndex'])->name('repair_types.index');
    Route::post('/repair-types', [AdminTicketController::class, 'repairTypesStore'])->name('repair_types.store');
});

// Flutter Web Redirect / Serve helper
Route::get('/flutter', function () {
    return redirect('/flutter/index.html');
});

/*
|--------------------------------------------------------------------------
| Raw JSON API Explorer (Strict Requirement: 100% Pure JSON, NO HTML/CSS)
|--------------------------------------------------------------------------
*/
Route::get('/api-docs', function () {
    $docs = [
        'system' => 'ระบบติดตามสถานะร้านซ่อมมือถือ (Mobile Phone Repair Status Tracking System)',
        'version' => '1.0.0',
        'framework' => 'Laravel 11.x',
        'base_url' => url('/api'),
        'authentication' => [
            'type' => 'Bearer Token (Laravel Sanctum)',
            'header' => 'Authorization: Bearer <token>',
            'notes' => 'Token ได้รับจากการเรียก POST /api/login หรือ POST /api/register',
            'test_tokens' => [
                'admin' => 'admin-test-token-2026',
                'user' => 'user-test-token-2026',
            ],
        ],
        'database_schema' => [
            'tables_count' => 4,
            'total_fields' => 24,
            'tables' => [
                'users' => ['id', 'name', 'email', 'password', 'phone', 'role'],
                'repair_types' => ['id', 'name', 'base_price', 'created_at'],
                'repair_tickets' => ['id', 'tracking_code', 'user_id', 'repair_type_id', 'device_model', 'issue_description', 'status', 'created_at'],
                'repair_status_logs' => ['id', 'ticket_id', 'status', 'note', 'cost', 'created_at'],
            ],
        ],
        'endpoints' => [
            [
                'group' => 'Authentication',
                'method' => 'POST',
                'uri' => '/api/register',
                'description' => 'สมัครสมาชิกผู้ใช้งานใหม่ (Default role: user)',
                'access' => 'Public',
                'request_body' => [
                    'name' => 'string (required, e.g. สมชาย ใจดี)',
                    'email' => 'string (required, unique, e.g. user@repairsystem.com)',
                    'phone' => 'string (required, unique, e.g. 0898765432)',
                    'password' => 'string (required, min 6, e.g. user1234)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'สมัครสมาชิกสำเร็จ ยินดีต้อนรับเข้าสู่ระบบติดตามงานซ่อม',
                    'token' => '2|c8fa7d4e5f...',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => 2,
                        'name' => 'สมชาย ใจดี',
                        'email' => 'user@repairsystem.com',
                        'phone' => '0898765432',
                        'role' => 'user',
                    ],
                ],
            ],
            [
                'group' => 'Authentication',
                'method' => 'POST',
                'uri' => '/api/login',
                'description' => 'เข้าสู่ระบบเพื่อขอรับ Sanctum Bearer Token',
                'access' => 'Public',
                'request_body' => [
                    'email' => 'string (required, e.g. admin@repairsystem.com)',
                    'password' => 'string (required, e.g. admin1234)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'token' => '1|9a8b7c6d5e...',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => 1,
                        'name' => 'ช่างสมศักดิ์ มือโปร (Admin Technician)',
                        'email' => 'admin@repairsystem.com',
                        'phone' => '0812345678',
                        'role' => 'admin',
                    ],
                ],
            ],
            [
                'group' => 'Authentication',
                'method' => 'POST',
                'uri' => '/api/logout',
                'description' => 'ออกจากระบบและยกเลิก Sanctum Token',
                'access' => 'Authenticated (Bearer Token)',
                'request_body' => [],
                'response_example' => [
                    'success' => true,
                    'message' => 'ออกจากระบบเรียบร้อยแล้ว (Logged out successfully)',
                ],
            ],
            [
                'group' => 'Authentication',
                'method' => 'GET',
                'uri' => '/api/me',
                'description' => 'ดึงข้อมูลโปรไฟล์ผู้ใช้ปัจจุบัน',
                'access' => 'Authenticated (Bearer Token)',
                'response_example' => [
                    'success' => true,
                    'user' => [
                        'id' => 1,
                        'name' => 'ช่างสมศักดิ์ มือโปร',
                        'email' => 'admin@repairsystem.com',
                        'phone' => '0812345678',
                        'role' => 'admin',
                    ],
                ],
            ],
            [
                'group' => 'Repair Types',
                'method' => 'GET',
                'uri' => '/api/repair-types',
                'description' => 'รายการประเภทงานซ่อมและบริการพร้อมราคาเริ่มต้น',
                'access' => 'Public',
                'response_example' => [
                    'success' => true,
                    'count' => 5,
                    'data' => [
                        [
                            'id' => 1,
                            'name' => 'เปลี่ยนจอแสดงผล (LCD / OLED)',
                            'base_price' => '1500.00',
                            'created_at' => '2026-09-03T10:00:00.000000Z',
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Repair Types',
                'method' => 'POST',
                'uri' => '/api/repair-types',
                'description' => 'เพิ่มประเภทงานซ่อมใหม่ (สำหรับเจ้าหน้าที่ช่าง)',
                'access' => 'Admin Only (Bearer Token)',
                'request_body' => [
                    'name' => 'string (required, e.g. เปลี่ยนฟิล์มกระจกแท้)',
                    'base_price' => 'number (required, e.g. 290.00)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'เพิ่มประเภทงานซ่อมสำเร็จ',
                    'data' => [
                        'id' => 6,
                        'name' => 'เปลี่ยนฟิล์มกระจกแท้',
                        'base_price' => '290.00',
                    ],
                ],
            ],
            [
                'group' => 'Repair Tickets',
                'method' => 'GET',
                'uri' => '/api/tickets',
                'description' => 'รายการใบแจ้งซ่อม (Admin เห็นทั้งหมด / User เห็นเฉพาะของตนเอง)',
                'access' => 'Authenticated (Bearer Token)',
                'query_params' => [
                    'status' => 'optional enum: received, repairing, completed, cancelled',
                ],
                'response_example' => [
                    'success' => true,
                    'count' => 2,
                    'data' => [
                        [
                            'id' => 1,
                            'tracking_code' => 'REP-2026001',
                            'user_id' => 2,
                            'repair_type_id' => 1,
                            'device_model' => 'iPhone 13 Pro 128GB',
                            'issue_description' => 'จอแตกจากการตกกระแทก',
                            'status' => 'repairing',
                            'created_at' => '2026-09-11T10:30:00.000000Z',
                            'repair_type' => [
                                'id' => 1,
                                'name' => 'เปลี่ยนจอแสดงผล (LCD / OLED)',
                                'base_price' => '1500.00',
                            ],
                            'owner' => [
                                'id' => 2,
                                'name' => 'คุณสมชาย ใจดี',
                                'phone' => '0898765432',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Repair Tickets',
                'method' => 'POST',
                'uri' => '/api/tickets',
                'description' => 'ยื่นคำขอแจ้งซ่อม / เปิดใบรับเครื่องเข้าซ่อม',
                'access' => 'Authenticated (Bearer Token)',
                'request_body' => [
                    'repair_type_id' => 'integer (required, references repair_types.id)',
                    'device_model' => 'string (required, e.g. iPhone 14 Pro Max)',
                    'issue_description' => 'string (required, อาการเสีย)',
                    'initial_note' => 'string (optional)',
                    'estimated_cost' => 'number (optional, ค่าประเมินเริ่มต้น)',
                    'customer_id' => 'integer (optional, admin only - ระบุรหัสลูกค้า)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'บันทึกใบแจ้งซ่อมสำเร็จ',
                    'data' => [
                        'id' => 3,
                        'tracking_code' => 'REP-2026003',
                        'status' => 'received',
                        'device_model' => 'iPhone 14 Pro Max',
                    ],
                ],
            ],
            [
                'group' => 'Repair Tickets',
                'method' => 'GET',
                'uri' => '/api/tickets/{code}',
                'description' => 'ติดตามสถานะงานซ่อมแบบเรียลไทม์ (ค้นหาด้วย tracking_code หรือเบอร์โทร)',
                'access' => 'Public',
                'response_example' => [
                    'success' => true,
                    'data' => [
                        'id' => 1,
                        'tracking_code' => 'REP-2026001',
                        'device_model' => 'iPhone 13 Pro 128GB (Graphite)',
                        'issue_description' => 'จอแตกจากการตกกระแทก ทัชสกรีนได้บางจุด',
                        'status' => 'repairing',
                        'created_at' => '2026-09-11T10:30:00.000000Z',
                        'repair_type' => [
                            'id' => 1,
                            'name' => 'เปลี่ยนจอแสดงผล (LCD / OLED)',
                            'base_price' => '1500.00',
                        ],
                        'owner' => [
                            'id' => 2,
                            'name' => 'คุณสมชาย ใจดี',
                            'phone' => '0898765432',
                        ],
                        'status_logs' => [
                            [
                                'id' => 1,
                                'status' => 'received',
                                'note' => 'รับเครื่องเข้าสู่ระบบ ตรวจเช็กเบื้องต้น',
                                'cost' => '1500.00',
                                'created_at' => '2026-09-11T10:35:00.000000Z',
                            ],
                            [
                                'id' => 2,
                                'status' => 'repairing',
                                'note' => 'กำลังดำเนินการถอดเปลี่ยนหน้าจอ OLED แท้',
                                'cost' => '1500.00',
                                'created_at' => '2026-09-12T14:15:00.000000Z',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Repair Tickets',
                'method' => 'POST',
                'uri' => '/api/tickets/{id}/status',
                'description' => 'ช่างอัปเดตสถานะงานซ่อม บันทึกความคืบหน้า และประเมิน/ระบุค่าบริการจริง',
                'access' => 'Admin Only (Bearer Token)',
                'request_body' => [
                    'status' => 'enum (required: received, repairing, completed, cancelled)',
                    'note' => 'string (required, บันทึกการทำงานของช่าง)',
                    'cost' => 'number (optional, ค่าบริการหรือค่าอะไหล่จริง)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'อัปเดตสถานะและบันทึกประวัติความคืบหน้าสำเร็จ',
                    'data' => [
                        'id' => 1,
                        'tracking_code' => 'REP-2026001',
                        'status' => 'completed',
                    ],
                ],
            ],
            [
                'group' => 'Statistics',
                'method' => 'GET',
                'uri' => '/api/stats',
                'description' => 'สรุปภาพรวมสถิติงานซ่อม',
                'access' => 'Public',
                'response_example' => [
                    'success' => true,
                    'data' => [
                        'total' => 2,
                        'received' => 0,
                        'repairing' => 1,
                        'completed' => 1,
                        'cancelled' => 0,
                        'total_repair_types' => 5,
                    ],
                ],
            ],
        ],
    ];

    return response()->json($docs, 200, [
        'Content-Type' => 'application/json; charset=UTF-8'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});
