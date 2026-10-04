class RepairType {
  final int id;
  final String name;
  final double basePrice;
  final String? createdAt;

  RepairType({
    required this.id,
    required this.name,
    required this.basePrice,
    this.createdAt,
  });

  factory RepairType.fromJson(Map<String, dynamic> json) {
    return RepairType(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      name: json['name']?.toString() ?? '',
      basePrice: double.tryParse(json['base_price']?.toString() ?? '0') ?? 0.0,
      createdAt: json['created_at']?.toString(),
    );
  }
}
