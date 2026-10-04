import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'register_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;

  void _fillDemo(String email, String pass) {
    setState(() {
      _emailController.text = email;
      _passwordController.text = pass;
    });
  }

  Future<void> _handleLogin() async {
    if (_emailController.text.trim().isEmpty || _passwordController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('กรุณากรอกอีเมลและรหัสผ่าน')),
      );
      return;
    }

    setState(() => _isLoading = true);
    final res = await ApiService.login(_emailController.text.trim(), _passwordController.text.trim());
    setState(() => _isLoading = false);

    if (mounted) {
      if (res['success'] == true) {
        Navigator.pop(context, true);
      } else {
        final errorMsg = res['message']?.toString() ?? 'เข้าสู่ระบบไม่สำเร็จ';
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
        title: const Text('เข้าสู่ระบบงานซ่อม'),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Icon(Icons.phonelink_lock, size: 64, color: Colors.indigo),
            const SizedBox(height: 16),
            const Text(
              'เข้าสู่ระบบผู้ใช้งาน',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: Colors.indigo),
            ),
            const SizedBox(height: 8),
            const Text(
              'รองรับทั้งบัญชีช่างซ่อม (Admin) และลูกค้า (User)',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),
            const SizedBox(height: 24),

            // Quick Fill Demo Buttons (Material Standard Colors)
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.indigo.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.indigo.shade200),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('⚡ คลิกเพื่อทดสอบระบบด่วน:', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.indigo)),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      ActionChip(
                        avatar: const Icon(Icons.security, size: 16, color: Colors.amber),
                        label: const Text('Admin (ช่างซ่อม)', style: TextStyle(fontSize: 11)),
                        backgroundColor: Colors.white,
                        onPressed: () => _fillDemo('admin@repairsystem.com', 'admin1234'),
                      ),
                      ActionChip(
                        avatar: const Icon(Icons.person, size: 16, color: Colors.blue),
                        label: const Text('User (ลูกค้า)', style: TextStyle(fontSize: 11)),
                        backgroundColor: Colors.white,
                        onPressed: () => _fillDemo('user@repairsystem.com', 'user1234'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            TextField(
              controller: _emailController,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(
                labelText: 'อีเมลผู้ใช้งาน',
                prefixIcon: Icon(Icons.email, color: Colors.indigo),
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),

            TextField(
              controller: _passwordController,
              obscureText: true,
              decoration: const InputDecoration(
                labelText: 'รหัสผ่าน',
                prefixIcon: Icon(Icons.lock, color: Colors.indigo),
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 24),

            ElevatedButton.icon(
              onPressed: _isLoading ? null : _handleLogin,
              icon: _isLoading ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white)) : const Icon(Icons.login),
              label: const Text('เข้าสู่ระบบ', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.indigo,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            ),
            const SizedBox(height: 16),

            TextButton(
              onPressed: () {
                Navigator.push(context, MaterialPageRoute(builder: (_) => const RegisterScreen()));
              },
              child: const Text('ยังไม่มีบัญชีผู้ใช้งาน? สมัครสมาชิกที่นี่'),
            ),
          ],
        ),
      ),
    );
  }
}
