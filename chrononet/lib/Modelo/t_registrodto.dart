class RegistroDto {
  final int idRegistro;           // ID del registro
  final int tipo;                 // Tipo de registro (short -> int)
  final DateTime fechaHora;       // Fecha y hora del registro
  final String incidencia;        // Incidencia asociada
  final int recogido;             // Flag de recogido (short -> int)
  final String dni;               // DNI del usuario
  final String nombre;            // Nombre del usuario
  final String apellidos;         // Apellidos del usuario
  final String codigo;            // Código del usuario
  final String token;             // Token asociado
  final double? totalHoras;       // Total de horas (decimal -> double?)

  // Constructor
  RegistroDto({
    required this.idRegistro,
    required this.tipo,
    required this.fechaHora,
    required this.incidencia,
    required this.recogido,
    required this.dni,
    required this.nombre,
    required this.apellidos,
    required this.codigo,
    required this.token,
    this.totalHoras,
  });

  // Convertir desde JSON a objeto Dart
  factory RegistroDto.fromJson(Map<String, dynamic> json) {
    return RegistroDto(
      idRegistro: json['idRegistro'],
      tipo: json['tipo'],
      fechaHora: DateTime.parse(json['fechaHora']),
      incidencia: json['incidencia'],
      recogido: json['recogido'],
      dni: json['dni'],
      nombre: json['nombre'],
      apellidos: json['apellidos'],
      codigo: json['codigo'],
      token: json['token'],
      totalHoras: json['totalHoras'] != null ? (json['totalHoras'] as num).toDouble() : null,
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
      'dni': dni,
      'nombre': nombre,
      'apellidos': apellidos,
      'codigo': codigo,
      'token': token,
      'totalHoras': totalHoras,
    };
  }

  // Representación en cadena (similar a ToString en C#)
  @override
  String toString() {
    return 'RegistroDto { idRegistro: $idRegistro, tipo: $tipo, fechaHora: $fechaHora, incidencia: $incidencia, recogido: $recogido, dni: $dni, nombre: $nombre, apellidos: $apellidos, codigo: $codigo, token: $token, totalHoras: $totalHoras }';
  }
}
