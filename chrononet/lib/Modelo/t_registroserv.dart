class RegistroServ {
  final int idRegistro;           // ID del registro local
  final int idUsuario;            // ID del usuario en el servidor
  final int tipo;                 // Tipo de registro (entrada, salida, etc.)
  final DateTime fechaHora;       // Fecha y hora del registro
  final String incidencia;        // Incidencia asociada
  final String token;             // Token único del registro
  final String dni;               // DNI del usuario
  final String nombre;            // Nombre del usuario
  final String apellidos;         // Apellidos del usuario
  final String codigo;            // Código del usuario
  final int adminBool;            // Flag de administrador
  final double? totalHoras;       // Total de horas (opcional)

  // Constructor
  RegistroServ({
    required this.idRegistro,
    required this.idUsuario,
    required this.tipo,
    required this.fechaHora,
    required this.incidencia,
    required this.token,
    required this.dni,
    required this.nombre,
    required this.apellidos,
    required this.codigo,
    this.adminBool = 0,
    this.totalHoras,
  });

  // Convertir desde JSON a objeto Dart
  factory RegistroServ.fromJson(Map<String, dynamic> json) {
    return RegistroServ(
      idRegistro: json['idRegistro'],
      idUsuario: json['idUsuario'],
      tipo: json['tipo'],
      fechaHora: DateTime.parse(json['fechaHora']),
      incidencia: json['incidencia'],
      token: json['token'],
      dni: json['dni'],
      nombre: json['nombre'],
      apellidos: json['apellidos'],
      codigo: json['codigo'],
      adminBool: json['adminBool'] ?? 0,
      totalHoras: json['totalHoras'] != null ? (json['totalHoras'] as num).toDouble() : null,
    );
  }

  // Convertir a JSON
  Map<String, dynamic> toJson() {
    return {
      'idRegistro': idRegistro,
      'idUsuario': idUsuario,
      'tipo': tipo,
      'fechaHora': fechaHora.toIso8601String(),
      'incidencia': incidencia,
      'token': token,
      'dni': dni,
      'nombre': nombre,
      'apellidos': apellidos,
      'codigo': codigo,
      'adminBool': adminBool,
      'totalHoras': totalHoras,
    };
  }

  // Representación en cadena (similar a ToString en C#)
  @override
  String toString() {
    return 'RegistroServ { idRegistro: $idRegistro, idUsuario: $idUsuario, tipo: $tipo, fechaHora: $fechaHora, incidencia: $incidencia, token: $token, dni: $dni, nombre: $nombre, apellidos: $apellidos, codigo: $codigo, adminBool: $adminBool, totalHoras: $totalHoras }';
  }
}
