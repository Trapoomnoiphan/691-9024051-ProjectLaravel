import 'package:flutter/material.dart';
import '../models/repair_ticket.dart';

class TicketDetailScreen extends StatelessWidget {
  final RepairTicket ticket;

  const TicketDetailScreen({super.key, required this.ticket});

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

  @override
  Widget build(BuildContext context) {
    final typeName = ticket.repairType != null ? ticket.repairType!.name : '-';

    return Scaffold(
      appBar: AppBar(
        title: Text('รหัสซ่อม ' + ticket.trackingCode),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Card(
              elevation: 2,
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(ticket.deviceModel, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text("ประเภท: " + typeName, style: const TextStyle(fontSize: 13, color: Colors.indigo)),
                    const SizedBox(height: 10),
                    Text("อาการเสีย: " + ticket.issueDescription, style: const TextStyle(fontSize: 13, color: Colors.black87)),
                    const Divider(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('ค่าบริการรวม:'),
                        Text('฿' + ticket.totalCost.toStringAsFixed(2), style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.green)),
                      ],
                    )
                  ],
                ),
              ),
            ),
            const SizedBox(height: 20),

            const Text('📜 ไทม์ไลน์ความคืบหน้าของช่าง (Status Logs):', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Colors.indigo)),
            const SizedBox(height: 12),

            if (ticket.statusLogs.isEmpty)
              const Center(child: Text('ยังไม่มีประวัติสถานะ'))
            else
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: ticket.statusLogs.length,
                itemBuilder: (ctx, idx) {
                  final log = ticket.statusLogs[idx];
                  return Card(
                    margin: const EdgeInsets.only(bottom: 10),
                    child: ListTile(
                      leading: CircleAvatar(
                        backgroundColor: _getStatusColor(log.status),
                        child: const Icon(Icons.build, color: Colors.white, size: 18),
                      ),
                      title: Text(log.status.toUpperCase(), style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 4),
                          Text(log.note != null ? log.note! : '-', style: const TextStyle(fontSize: 12)),
                          if (log.cost != null)
                            Text('ค่าบริการ: ฿' + log.cost!.toStringAsFixed(2), style: const TextStyle(fontSize: 11, color: Colors.green, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      trailing: Text(log.createdAt != null ? log.createdAt! : '', style: const TextStyle(fontSize: 10, color: Colors.grey)),
                    ),
                  );
                },
              ),
          ],
        ),
      ),
    );
  }
}
