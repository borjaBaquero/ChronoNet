class TUsuario {
  final int idUsuario;        // ID del usuario
  final String? dni;          // DNI (puede ser nulo)
  final String codigo;        // Código
  final String? nombre;       // Nombre (puede ser nulo)
  final String? apellidos;    // Apellidos (puede ser nulo)
  final int adminBool;        // Flag de administrador

  // Constructor con parámetros obligatorios y opcionales
  TUsuario({
    required this.idUsuario,
    this.dni,
    required this.codigo,
    this.nombre,
    this.apellidos,
    this.adminBool = 0,
  });

  // Getter para NombreCompleto
  String get nombreCompleto => '${nombre ?? ''} ${apellidos ?? ''}';

  // Método para convertir un JSON a un objeto TUsuario
  factory TUsuario.fromJson(Map<String, dynamic> json) {
    return TUsuario(
      idUsuario: json['idUsuario'],
      dni: json['DNI'],
      codigo: json['Codigo'],
      nombre: json['Nombre'],
      apellidos: json['Apellidos'],
      adminBool: json['AdminBool'] ?? 0,
    );
  }

  // Método para convertir el objeto TUsuario a JSON
  Map<String, dynamic> toJson() {
    return {
      'idUsuario': idUsuario,
      'DNI': dni,
      'Codigo': codigo,
      'Nombre': nombre,
      'Apellidos': apellidos,
      'AdminBool': adminBool,
    };
  }
}
