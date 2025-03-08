class TRegistro {
  final int idRegistro;         // ID del registro
  final int tipo;               // 0 = Entrada, 1 = Salida, 2 = Pausa
  final DateTime fechaHora;     // Fecha y hora del registro
  final String incidencia;      // Descripción de incidencia
  final int recogido;           // Flag de recogido
  final int idUsuario;          // Relación con TUsuario
  final String token;           // Token asociado

  // Constructor
  TRegistro({
    required this.idRegistro,
    required this.tipo,
    required this.fechaHora,
    required this.incidencia,
    required this.recogido,
    required this.idUsuario,
    required this.token,
  });

  // Convertir de JSON a objeto Dart
  factory TRegistro.fromJson(Map<String, dynamic> json) {
    return TRegistro(
      idRegistro: json['idRegistro'],
      tipo: json['tipo'],
      fechaHora: DateTime.parse(json['fechaHora']),
      incidencia: json['incidencia'],
      recogido: json['recogido'],
      idUsuario: json['idUsuario'],
      token: json['token'],
    );
  }

  // Convertir a JSON
  Map<String, dynamic> toJson() {
    return {
      'idRegistro': idRegistro,
      'tipo': tipo,
      'fechaHora': fechaHora.toIso8601String(),
      'incidencia': incidencia,
      'recogido': recogido,
      'idUsuario': idUsuario,
      'token': token,
    };
  }
}
