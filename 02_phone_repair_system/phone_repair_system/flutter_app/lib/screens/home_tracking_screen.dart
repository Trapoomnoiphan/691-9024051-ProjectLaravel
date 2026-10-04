import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/repair_ticket.dart';
import 'ticket_detail_screen.dart';
import 'create_ticket_screen.dart';
import 'login_screen.dart';
import 'admin_dashboard_screen.dart';

class HomeTrackingScreen extends StatefulWidget {
  const HomeTrackingScreen({super.key});

  @override
  State<HomeTrackingScreen> createState() => _HomeTrackingScreenState();
}

class _HomeTrackingScreenState extends State<HomeTrackingScreen> {
  final _searchController = TextEditingController(text: 'REP-2026001');
  RepairTicket? _ticket;
  bool _isLoading = false;
  String? _errorMsg;

  @override
  void initState() {
    super.initState();
    _handleSearch();
  }

  Future<void> _handleSearch() async {
    final q = _searchController.text.trim();
    if (q.isEmpty) return;

    setState(() {
      _isLoading = true;
      _errorMsg = null;
    });

    final ticket = await ApiService.trackTicket(q);

    setState(() {
      _isLoading = false;
      _ticket = ticket;
      if (ticket == null) {
        _errorMsg = "ไม่พบข้อมูลงานซ่อมสำหรับรหัสหรือเบอร์: " + q;
      }
    });
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'received':
        return Colors.blue;
      case 'repairing':
        return Colors.orange;
      case 'completed':
        return Colors.green;
      case 'cancelled':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'received':
        return 'รับเครื่องเข้าซ่อม';
      case 'repairing':
        return 'กำลังดำเนินการซ่อม';
      case 'completed':
        return 'ซ่อมเสร็จสิ้น / พร้อมรับเครื่อง';
      case 'cancelled':
        return 'ยกเลิกการซ่อม';
      default:
        return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = ApiService.currentUser;

    return Scaffold(
      appBar: AppBar(
        title: const Text('PhoneFix Pro (ระบบติดตามงานซ่อม)', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
        actions: [
          if (user != null && user.isAdmin)
            IconButton(
              icon: const Icon(Icons.dashboard),
              tooltip: 'แผงควบคุมช่าง (Admin)',
              onPressed: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const AdminDashboardScreen()));
              },
            ),
          IconButton(
            icon: Icon(user != null ? Icons.logout : Icons.login),
            tooltip: user != null ? 'ออกจากระบบ' : 'เข้าสู่ระบบ',
            onPressed: () async {
              if (user != null) {
                await ApiService.logout();
                setState(() {});
              } else {
                await Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
                setState(() {});
              }
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Hero Search Card
            Card(
              elevation: 3,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              color: Colors.indigo.shade900,
              child: Padding(
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  children: [
                    const Text(
                      '🔍 ตรวจสอบและติดตามสถานะงานซ่อม',
                      style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'กรอกรหัสติดตามงานซ่อม หรือเบอร์โทรศัพท์ที่แจ้งซ่อมไว้',
                      style: TextStyle(color: Colors.white70, fontSize: 12),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: TextField(
                            controller: _searchController,
                            style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
                            decoration: InputDecoration(
                              fillColor: Colors.white,
                              filled: true,
                              hintText: 'เช่น REP-2026001',
                              prefixIcon: const Icon(Icons.search, color: Colors.indigo),
                              border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
                              contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        ElevatedButton(
                          onPressed: _isLoading ? null : _handleSearch,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.amber,
                            foregroundColor: Colors.black,
                            padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          child: _isLoading
                              ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))
                              : const Text('ค้นหา', style: TextStyle(fontWeight: FontWeight.bold)),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Text('ตัวอย่าง: ', style: TextStyle(color: Colors.white60, fontSize: 11)),
                        GestureDetector(
                          onTap: () {
                            _searchController.text = 'REP-2026001';
                            _handleSearch();
                          },
                          child: const Text('REP-2026001 ', style: TextStyle(color: Colors.amber, fontSize: 11, fontWeight: FontWeight.bold, decoration: TextDecoration.underline)),
                        ),
                        const Text('| ', style: TextStyle(color: Colors.white60, fontSize: 11)),
                        GestureDetector(
                          onTap: () {
                            _searchController.text = 'REP-2026002';
                            _handleSearch();
                          },
                          child: const Text('REP-2026002', style: TextStyle(color: Colors.amber, fontSize: 11, fontWeight: FontWeight.bold, decoration: TextDecoration.underline)),
                        ),
                      ],
                    )
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),

            if (_errorMsg != null)
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: Text(_errorMsg!, textAlign: TextAlign.center, style: TextStyle(color: Colors.red.shade800, fontSize: 13)),
              ),

            if (_ticket != null) ...[
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            _ticket!.trackingCode,
                            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, fontFamily: 'monospace', color: Colors.indigo),
                          ),
                          Chip(
                            label: Text(_getStatusText(_ticket!.status), style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                            backgroundColor: _getStatusColor(_ticket!.status),
                          ),
                        ],
                      ),
                      const Divider(),
                      const SizedBox(height: 4),

                      Text("อุปกรณ์: " + _ticket!.deviceModel, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
                      Text("ประเภทงานซ่อม: " + (_ticket!.repairType != null ? _ticket!.repairType!.name : '-'), style: const TextStyle(fontSize: 13, color: Colors.indigo)),
                      const SizedBox(height: 8),

                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(8)),
                        child: Text("อาการเสีย: " + _ticket!.issueDescription, style: const TextStyle(fontSize: 12, color: Colors.black87)),
                      ),
                      const SizedBox(height: 12),

                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('ค่าบริการรวมปัจจุบัน:', style: TextStyle(fontSize: 13, color: Colors.grey)),
                          Text(
                            '฿' + _ticket!.totalCost.toStringAsFixed(2),
                            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.green),
                          ),
                        ],
                      ),
                      const SizedBox(height: 14),

                      ElevatedButton.icon(
                        onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (_) => TicketDetailScreen(ticket: _ticket!)));
                        },
                        icon: const Icon(Icons.timeline, size: 16),
                        label: const Text('ดูไทม์ไลน์ความคืบหน้าของช่าง'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.indigo,
                          foregroundColor: Colors.white,
                          minimumSize: const Size.fromHeight(40),
                        ),
                      )
                    ],
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: const Text('ยื่นแจ้งซ่อมใหม่'),
        onPressed: () async {
          if (!ApiService.isAuthenticated) {
            final loggedIn = await Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
            if (loggedIn != true) return;
          }
          if (context.mounted) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => const CreateTicketScreen()));
          }
        },
      ),
    );
  }
}
