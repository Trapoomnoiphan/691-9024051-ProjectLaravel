import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/repair_ticket.dart';
import 'ticket_detail_screen.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  List<RepairTicket> _tickets = [];
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadTickets();
  }

  Future<void> _loadTickets({String? status}) async {
    setState(() => _isLoading = true);
    final tickets = await ApiService.getTickets(status: status);
    setState(() {
      _isLoading = false;
      _tickets = tickets;
    });
  }

  void _showUpdateDialog(RepairTicket ticket) {
    String selectedStatus = ticket.status;
    final costController = TextEditingController(text: ticket.totalCost.toStringAsFixed(2));
    final noteController = TextEditingController();

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDialogState) => AlertDialog(
          title: Text('อัปเดตสถานะ: ' + ticket.trackingCode, style: const TextStyle(fontSize: 16)),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                DropdownButtonFormField<String>(
                  value: selectedStatus,
                  decoration: const InputDecoration(labelText: 'สถานะงานซ่อม'),
                  items: const [
                    DropdownMenuItem(value: 'received', child: Text('📥 รับเครื่องเข้าซ่อม')),
                    DropdownMenuItem(value: 'repairing', child: Text('🔧 กำลังดำเนินการซ่อม')),
                    DropdownMenuItem(value: 'completed', child: Text('✅ ซ่อมเสร็จสิ้น')),
                    DropdownMenuItem(value: 'cancelled', child: Text('❌ ยกเลิก')),
                  ],
                  onChanged: (val) {
                    if (val != null) setDialogState(() => selectedStatus = val);
                  },
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: costController,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(labelText: 'ค่าบริการจริง (บาท)'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: noteController,
                  maxLines: 2,
                  decoration: const InputDecoration(labelText: 'บันทึกการปฏิบัติงานของช่าง'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('ยกเลิก'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: Colors.indigo, foregroundColor: Colors.white),
              onPressed: () async {
                if (noteController.text.trim().isEmpty) return;
                Navigator.pop(ctx);
                final cost = double.tryParse(costController.text.trim());
                await ApiService.updateTicketStatus(
                  ticketId: ticket.id,
                  status: selectedStatus,
                  note: noteController.text.trim(),
                  cost: cost,
                );
                _loadTickets();
              },
              child: const Text('บันทึก'),
            )
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('แผงควบคุมช่าง (Admin Dashboard)'),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: [
          // Filter Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Row(
              children: [
                ActionChip(label: const Text('ทั้งหมด'), onPressed: () => _loadTickets()),
                const SizedBox(width: 6),
                ActionChip(label: const Text('📥 รับเครื่อง'), onPressed: () => _loadTickets(status: 'received')),
                const SizedBox(width: 6),
                ActionChip(label: const Text('🔧 กำลังซ่อม'), onPressed: () => _loadTickets(status: 'repairing')),
                const SizedBox(width: 6),
                ActionChip(label: const Text('✅ เสร็จสิ้น'), onPressed: () => _loadTickets(status: 'completed')),
              ],
            ),
          ),
          const Divider(height: 1),

          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _tickets.isEmpty
                    ? const Center(child: Text('ไม่มีรายการงานซ่อม'))
                    : ListView.builder(
                        itemCount: _tickets.length,
                        itemBuilder: (ctx, idx) {
                          final t = _tickets[idx];
                          final ownerName = t.owner != null ? t.owner!.name : '-';
                          return Card(
                            margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            child: ListTile(
                              title: Text(t.trackingCode + ' - ' + t.deviceModel, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                              subtitle: Text('ลูกค้า: ' + ownerName + ' | สถานะ: ' + t.status + ' | ฿' + t.totalCost.toStringAsFixed(2), style: const TextStyle(fontSize: 12)),
                              trailing: IconButton(
                                icon: const Icon(Icons.edit, color: Colors.indigo),
                                onPressed: () => _showUpdateDialog(t),
                              ),
                              onTap: () {
                                Navigator.push(context, MaterialPageRoute(builder: (_) => TicketDetailScreen(ticket: t)));
                              },
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
