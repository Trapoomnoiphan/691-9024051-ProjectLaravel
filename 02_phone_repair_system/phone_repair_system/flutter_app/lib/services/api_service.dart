import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import '../models/repair_type.dart';
import '../models/repair_ticket.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8080/api';

  static String? _token;
  static User? _currentUser;

  static User? get currentUser => _currentUser;
  static String? get token => _token;
  static bool get isAuthenticated => _token != null && _token!.isNotEmpty;

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    if (_token != null) {
      await getMe();
    }
  }

  static Map<String, String> _headers() {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (_token != null) {
      headers['Authorization'] = 'Bearer ' + _token!;
    }
    return headers;
  }

  // Auth: Login
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse(baseUrl + '/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );
      final data = jsonDecode(res.body);
      if (res.statusCode == 200 && data['success'] == true) {
        _token = data['token'];
        _currentUser = User.fromJson(data['user']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
        return {'success': true, 'user': _currentUser};
      }
      final msg = data['message'] != null ? data['message'].toString() : 'เข้าสู่ระบบไม่สำเร็จ';
      return {'success': false, 'message': msg};
    } catch (e) {
      return {'success': false, 'message': 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + e.toString()};
    }
  }

  // Auth: Register
  static Future<Map<String, dynamic>> register(String name, String phone, String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse(baseUrl + '/register'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'name': name,
          'phone': phone,
          'email': email,
          'password': password,
        }),
      );
      final data = jsonDecode(res.body);
      if (res.statusCode == 201 && data['success'] == true) {
        _token = data['token'];
        _currentUser = User.fromJson(data['user']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
        return {'success': true, 'user': _currentUser};
      }
      final msg = data['message'] != null ? data['message'].toString() : 'สมัครสมาชิกไม่สำเร็จ';
      return {'success': false, 'message': msg};
    } catch (e) {
      return {'success': false, 'message': 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + e.toString()};
    }
  }

  // Auth: Logout
  static Future<void> logout() async {
    if (_token != null) {
      try {
        await http.post(Uri.parse(baseUrl + '/logout'), headers: _headers());
      } catch (_) {}
    }
    _token = null;
    _currentUser = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  // Auth: Get Current Profile
  static Future<User?> getMe() async {
    if (_token == null) return null;
    try {
      final res = await http.get(Uri.parse(baseUrl + '/me'), headers: _headers());
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        _currentUser = User.fromJson(data['user']);
        return _currentUser;
      }
    } catch (_) {}
    return null;
  }

  // Repair Types
  static Future<List<RepairType>> getRepairTypes() async {
    try {
      final res = await http.get(Uri.parse(baseUrl + '/repair-types'));
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        final list = data['data'] as List<dynamic>;
        return list.map((e) => RepairType.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }

  // Track Ticket by Code or Phone
  static Future<RepairTicket?> trackTicket(String query) async {
    try {
      final res = await http.get(Uri.parse(baseUrl + '/tickets/' + Uri.encodeComponent(query)));
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        if (data['success'] == true && data['data'] != null) {
          return RepairTicket.fromJson(data['data']);
        }
      }
    } catch (_) {}
    return null;
  }

  // Get Tickets List (Authenticated)
  static Future<List<RepairTicket>> getTickets({String? status}) async {
    try {
      String url = baseUrl + '/tickets';
      if (status != null && status.isNotEmpty) {
        url += '?status=' + status;
      }
      final res = await http.get(Uri.parse(url), headers: _headers());
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        final list = data['data'] as List<dynamic>;
        return list.map((e) => RepairTicket.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }

  // Create Ticket (Submit Repair)
  static Future<Map<String, dynamic>> createTicket({
    required int repairTypeId,
    required String deviceModel,
    required String issueDescription,
  }) async {
    try {
      final res = await http.post(
        Uri.parse(baseUrl + '/tickets'),
        headers: _headers(),
        body: jsonEncode({
          'repair_type_id': repairTypeId,
          'device_model': deviceModel,
          'issue_description': issueDescription,
        }),
      );
      final data = jsonDecode(res.body);
      if (res.statusCode == 201 && data['success'] == true) {
        return {'success': true, 'ticket': RepairTicket.fromJson(data['data'])};
      }
      final msg = data['message'] != null ? data['message'].toString() : 'ยื่นแจ้งซ่อมไม่สำเร็จ';
      return {'success': false, 'message': msg};
    } catch (e) {
      return {'success': false, 'message': 'เชื่อมต่อล้มเหลว: ' + e.toString()};
    }
  }

  // Update Status (Admin Only)
  static Future<bool> updateTicketStatus({
    required int ticketId,
    required String status,
    required String note,
    double? cost,
  }) async {
    try {
      final body = {'status': status, 'note': note};
      if (cost != null) body['cost'] = cost.toString();

      final res = await http.post(
        Uri.parse(baseUrl + '/tickets/' + ticketId.toString() + '/status'),
        headers: _headers(),
        body: jsonEncode(body),
      );
      return res.statusCode == 200;
    } catch (_) {
      return false;
    }
  }
}
