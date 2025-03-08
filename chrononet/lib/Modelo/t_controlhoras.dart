class TControlHoras {
  final int idControlHoras;       // ID de control de horas
  final double totalHoras;        // Total de horas trabajadas (decimal -> double)
  final DateTime fecha;           // Fecha asociada
  final String token;             // Token asociado

  // Constructor
  TControlHoras({
    required this.idControlHoras,
    required this.totalHoras,
    required this.fecha,
    required this.token,
  });

  // Constructor vacío opcional
  TControlHoras.empty()
      : idControlHoras = 0,
        totalHoras = 0.0,
        fecha = DateTime.now(),
        token = '';

  // Método para convertir desde JSON a un objeto Dart
  factory TControlHoras.fromJson(Map<String, dynamic> json) {
    return TControlHoras(
      idControlHoras: json['idControlHoras'],
      totalHoras: (json['totalHoras'] as num).toDouble(),
      fecha: DateTime.parse(json['fecha']),
      token: json['token'],
    );
  }

  // Método para convertir un objeto Dart a JSON
  Map<String, dynamic> toJson() {
    return {
      'idControlHoras': idControlHoras,
      'totalHoras': totalHoras,
      'fecha': fecha.toIso8601String(),
      'token': token,
    };
  }

  // Representación en cadena (similar a ToString en C#)
  @override
  String toString() {
    return 'Id: $idControlHoras, Horas: $totalHoras, Fecha: ${fecha.toLocal()}, Token: $token';
  }
}
