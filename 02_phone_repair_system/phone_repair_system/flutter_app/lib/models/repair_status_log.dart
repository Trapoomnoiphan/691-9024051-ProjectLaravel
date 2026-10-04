class RepairStatusLog {
  final int id;
  final int ticketId;
  final String status;
  final String? note;
  final double? cost;
  final String? createdAt;

  RepairStatusLog({
    required this.id,
    required this.ticketId,
    required this.status,
    this.note,
    this.cost,
    this.createdAt,
  });

  factory RepairStatusLog.fromJson(Map<String, dynamic> json) {
    return RepairStatusLog(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      ticketId: json['ticket_id'] is int ? json['ticket_id'] : int.parse(json['ticket_id'].toString()),
      status: json['status']?.toString() ?? '',
      note: json['note']?.toString(),
      cost: json['cost'] != null ? double.tryParse(json['cost'].toString()) : null,
      createdAt: json['created_at']?.toString(),
    );
  }
}
