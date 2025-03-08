class TRegistrado {
  final String nombre;              // Nombre del usuario
  final String apellidos;           // Apellidos del usuario
  final int idRegistro;             // ID del registro
  final int idUsuario;              // ID del usuario
  final String tipo;                // Tipo del registro
  final DateTime fechaHora;         // Fecha y hora
  final String incidencia;          // Descripción de la incidencia
  final Duration horasTrabajadas;   // Duración de horas trabajadas
  Duration tiempoPausado;           // Tiempo pausado (inicializado en cero)

  // Constructor
  TRegistrado({
    required this.nombre,
    required this.apellidos,
    required this.idRegistro,
    required this.idUsuario,
    required this.tipo,
    required this.fechaHora,
    required this.incidencia,
    required this.horasTrabajadas,
    this.tiempoPausado = Duration.zero, // Inicializamos en Duration.zero
  });

  // Getter para NombreCompleto
  String get nombreCompleto => '$nombre $apellidos';

  // Getter para HorasTrabajadasFormato en formato "00:00"
  String get horasTrabajadasFormato {
    final int horas = horasTrabajadas.inHours;
    final int minutos = horasTrabajadas.inMinutes.remainder(60);
    return '${horas.toString().padLeft(2, '0')}:${minutos.toString().padLeft(2, '0')}';
  }

  // Convertir de JSON a objeto Dart
  factory TRegistrado.fromJson(Map<String, dynamic> json) {
    return TRegistrado(
      nombre: json['nombre'],
      apellidos: json['apellidos'],
      idRegistro: json['idRegistro'],
      idUsuario: json['idUsuario'],
      tipo: json['tipo'],
      fechaHora: DateTime.parse(json['fechaHora']),
      incidencia: json['incidencia'],
      horasTrabajadas: Duration(minutes: json['horasTrabajadas']),
      tiempoPausado: Duration(minutes: json['tiempoPausado'] ?? 0),
    );
  }

  // Convertir a JSON
  Map<String, dynamic> toJson() {
    return {
      'nombre': nombre,
      'apellidos': apellidos,
      'idRegistro': idRegistro,
      'idUsuario': idUsuario,
      'tipo': tipo,
      'fechaHora': fechaHora.toIso8601String(),
      'incidencia': incidencia,
      'horasTrabajadas': horasTrabajadas.inMinutes,
      'tiempoPausado': tiempoPausado.inMinutes,
    };
  }
}
