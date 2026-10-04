import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../models/repair_type.dart';

class CreateTicketScreen extends StatefulWidget {
  const CreateTicketScreen({super.key});

  @override
  State<CreateTicketScreen> createState() => _CreateTicketScreenState();
}

class _CreateTicketScreenState extends State<CreateTicketScreen> {
  final _deviceModelController = TextEditingController();
  final _issueDescController = TextEditingController();
  List<RepairType> _repairTypes = [];
  int? _selectedTypeId;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _loadRepairTypes();
  }

  Future<void> _loadRepairTypes() async {
    final types = await ApiService.getRepairTypes();
    setState(() {
      _repairTypes = types;
      if (types.isNotEmpty) {
        _selectedTypeId = types.first.id;
      }
    });
  }

  Future<void> _submitTicket() async {
    if (_selectedTypeId == null ||
        _deviceModelController.text.trim().isEmpty ||
        _issueDescController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('กรุณากรอกข้อมูลให้ครบถ้วน')),
      );
      return;
    }

    setState(() => _isLoading = true);
    final res = await ApiService.createTicket(
      repairTypeId: _selectedTypeId!,
      deviceModel: _deviceModelController.text.trim(),
      issueDescription: _issueDescController.text.trim(),
    );
    setState(() => _isLoading = false);

    if (mounted) {
      if (res['success'] == true) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('ยื่นคำขอแจ้งซ่อมสำเร็จ!')),
        );
        Navigator.pop(context, true);
      } else {
        final errorMsg = res['message']?.toString() ?? 'เกิดข้อผิดพลาด';
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(errorMsg)),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('ยื่นคำขอแจ้งซ่อมออนไลน์'),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'กรอกข้อมูลส่งเครื่องเข้าซ่อม',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.indigo),
            ),
            const SizedBox(height: 16),

            DropdownButtonFormField<int>(
              value: _selectedTypeId,
              decoration: const InputDecoration(
                labelText: 'เลือกประเภทการซ่อม *',
                border: OutlineInputBorder(),
              ),
              items: _repairTypes.map((t) {
                return DropdownMenuItem<int>(
                  value: t.id,
                  child: Text(t.name + ' (฿' + t.basePrice.toStringAsFixed(2) + ')'),
                );
              }).toList(),
              onChanged: (val) => setState(() => _selectedTypeId = val),
            ),
            const SizedBox(height: 14),

            TextField(
              controller: _deviceModelController,
              decoration: const InputDecoration(
                labelText: 'ยี่ห้อและรุ่นของอุปกรณ์ *',
                hintText: 'เช่น iPhone 13 Pro 128GB หรือ Galaxy S23',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 14),

            TextField(
              controller: _issueDescController,
              maxLines: 4,
              decoration: const InputDecoration(
                labelText: 'รายละเอียดอาการเสีย *',
                hintText: 'อธิบายอาการ เช่น หน้าจอแตก ทัชสกรีนไม่ได้ ชาร์จไม่เข้า...',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 24),

            ElevatedButton(
              onPressed: _isLoading ? null : _submitTicket,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.indigo,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: _isLoading
                  ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Text('ยืนยันส่งคำขอแจ้งซ่อม', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}
