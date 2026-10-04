<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\RepairType;
use App\Models\RepairTicket;
use App\Models\RepairStatusLog;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (1 Admin, 1 Customer User)
        $admin = User::create([
            'name' => 'ช่างสมศักดิ์ มือโปร (Admin Technician)',
            'email' => 'admin@repairsystem.com',
            'password' => Hash::make('admin1234'),
            'phone' => '0812345678',
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'คุณสมชาย ใจดี (Customer User)',
            'email' => 'user@repairsystem.com',
            'password' => Hash::make('user1234'),
            'phone' => '0898765432',
            'role' => 'user',
        ]);

        // 2. Seed Repair Types
        $types = [
            [
                'name' => 'เปลี่ยนจอแสดงผล (LCD / OLED)',
                'base_price' => 1500.00,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'name' => 'เปลี่ยนแบตเตอรี่ (Battery Replacement)',
                'base_price' => 850.00,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'name' => 'ซ่อมเมนบอร์ด / ไอซีเพาเวอร์ (Motherboard & IC)',
                'base_price' => 2200.00,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'name' => 'ล้างเครื่อง / อัปเดตซอฟต์แวร์ (OS & Software Restore)',
                'base_price' => 450.00,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'name' => 'ซ่อมระบบชาร์จ / พอร์ต Type-C / Lightning',
                'base_price' => 650.00,
                'created_at' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($types as $typeData) {
            RepairType::create($typeData);
        }

        $screenType = RepairType::where('name', 'like', '%เปลี่ยนจอ%')->first();
        $batteryType = RepairType::where('name', 'like', '%เปลี่ยนแบตเตอรี่%')->first();

        // 3. Seed Sample Tickets
        // Ticket 1: Active repair (status: repairing)
        $ticket1 = RepairTicket::create([
            'tracking_code' => 'REP-2026001',
            'user_id' => $user->id,
            'repair_type_id' => $screenType->id,
            'device_model' => 'iPhone 13 Pro 128GB (Graphite)',
            'issue_description' => 'จอแตกจากการตกกระแทก ทัชสกรีนได้บางจุด มีเส้นสีเขียวพาดกลางจอ',
            'status' => 'repairing',
            'created_at' => Carbon::now()->subDays(2)->setHour(10)->setMinute(30),
        ]);

        RepairStatusLog::create([
            'ticket_id' => $ticket1->id,
            'status' => 'received',
            'note' => 'รับเครื่องเข้าสู่ระบบ ตรวจเช็กเบื้องต้น จอแตกชัดเจน เมนบอร์ดและกล้องทำงานปกติ',
            'cost' => 1500.00,
            'created_at' => Carbon::now()->subDays(2)->setHour(10)->setMinute(35),
        ]);

        RepairStatusLog::create([
            'ticket_id' => $ticket1->id,
            'status' => 'repairing',
            'note' => 'กำลังดำเนินการถอดเปลี่ยนหน้าจอ OLED แท้ พร้อมซีลยางกันน้ำมาตรฐานโรงงาน',
            'cost' => 1500.00,
            'created_at' => Carbon::now()->subDay(1)->setHour(14)->setMinute(15),
        ]);

        // Ticket 2: Completed repair (status: completed)
        $ticket2 = RepairTicket::create([
            'tracking_code' => 'REP-2026002',
            'user_id' => $user->id,
            'repair_type_id' => $batteryType->id,
            'device_model' => 'Samsung Galaxy S23 (Phantom Black)',
            'issue_description' => 'แบตเตอรี่เสื่อมสภาพ สุขภาพแบต 68% ใช้งานได้ไม่ถึง 2 ชั่วโมงเครื่องดับเอง',
            'status' => 'completed',
            'created_at' => Carbon::now()->subDays(5)->setHour(11)->setMinute(0),
        ]);

        RepairStatusLog::create([
            'ticket_id' => $ticket2->id,
            'status' => 'received',
            'note' => 'รับเครื่องตรวจวัดค่าความจุแบตเตอรี่ พบค่าความต้านทานสูง ควรเปลี่ยนแบตเตอรี่ใหม่',
            'cost' => 850.00,
            'created_at' => Carbon::now()->subDays(5)->setHour(11)->setMinute(10),
        ]);

        RepairStatusLog::create([
            'ticket_id' => $ticket2->id,
            'status' => 'repairing',
            'note' => 'เปลี่ยนแบตเตอรี่ใหม่ความจุเต็ม 3900 mAh ผ่านการทดสอบวงจรชาร์จไฟ',
            'cost' => 850.00,
            'created_at' => Carbon::now()->subDays(4)->setHour(15)->setMinute(20),
        ]);

        RepairStatusLog::create([
            'ticket_id' => $ticket2->id,
            'status' => 'completed',
            'note' => 'ซ่อมเสร็จสมบูรณ์ ทดสอบชาร์จ 100% ใช้งานปกติ ปิดฝาหลังเรียบร้อย พร้อมส่งมอบลูกค้า',
            'cost' => 850.00,
            'created_at' => Carbon::now()->subDays(3)->setHour(16)->setMinute(45),
        ]);
    }
}
