import 'user.dart';
import 'repair_type.dart';
import 'repair_status_log.dart';

class RepairTicket {
  final int id;
  final String trackingCode;
  final int userId;
  final int repairTypeId;
  final String deviceModel;
  final String issueDescription;
  final String status;
  final String? createdAt;
  final User? owner;
  final RepairType? repairType;
  final List<RepairStatusLog> statusLogs;

  RepairTicket({
    required this.id,
    required this.trackingCode,
    required this.userId,
    required this.repairTypeId,
    required this.deviceModel,
    required this.issueDescription,
    required this.status,
    this.createdAt,
    this.owner,
    this.repairType,
    required this.statusLogs,
  });

  double get totalCost {
    if (statusLogs.isNotEmpty) {
      for (final log in statusLogs.reversed) {
        if (log.cost != null) {
          return log.cost!;
        }
      }
    }
    return repairType?.basePrice ?? 0.0;
  }

  factory RepairTicket.fromJson(Map<String, dynamic> json) {
    final rawLogs = json['status_logs'] as List<dynamic>? ?? [];
    return RepairTicket(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      trackingCode: json['tracking_code']?.toString() ?? '',
      userId: json['user_id'] is int ? json['user_id'] : int.parse(json['user_id'].toString()),
      repairTypeId: json['repair_type_id'] is int ? json['repair_type_id'] : int.parse(json['repair_type_id'].toString()),
      deviceModel: json['device_model']?.toString() ?? '',
      issueDescription: json['issue_description']?.toString() ?? '',
      status: json['status']?.toString() ?? 'received',
      createdAt: json['created_at']?.toString(),
      owner: json['owner'] != null ? User.fromJson(json['owner']) : null,
      repairType: json['repair_type'] != null ? RepairType.fromJson(json['repair_type']) : null,
      statusLogs: rawLogs.map((l) => RepairStatusLog.fromJson(l)).toList(),
    );
  }
}
