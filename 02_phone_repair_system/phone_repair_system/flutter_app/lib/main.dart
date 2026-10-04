import 'package:flutter/material.dart';
import 'services/api_service.dart';
import 'screens/home_tracking_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await ApiService.init();
  runApp(const PhoneRepairApp());
}

class PhoneRepairApp extends StatelessWidget {
  const PhoneRepairApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'PhoneFix Pro - ระบบติดตามงานซ่อมมือถือ',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: Colors.indigo,
          primary: Colors.indigo,
          secondary: Colors.lightBlue,
        ),
        fontFamily: 'Prompt',
      ),
      home: const HomeTrackingScreen(),
    );
  }
}
