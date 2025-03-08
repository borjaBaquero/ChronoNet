import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:shared_preferences/shared_preferences.dart'; // Importar SharedPreferences
import 'dart:convert';
import 'dart:io';

// Importa la clase FichajePage desde la carpeta Vista
import 'fichaje.dart';

class InicioPage extends StatefulWidget {
  @override
  _InicioPageState createState() => _InicioPageState();
}

class _InicioPageState extends State<InicioPage> {
  final TextEditingController dniController = TextEditingController();
  final TextEditingController codigoController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadSavedCredentials(); // Cargar credenciales guardadas al iniciar
  }

  /// Función para cargar credenciales guardadas
  Future<void> _loadSavedCredentials() async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      dniController.text = prefs.getString('dni') ?? '';
      codigoController.text = prefs.getString('codigo') ?? '';
    });
  }

  /// Función para guardar credenciales
  Future<void> _saveCredentials(String dni, String codigo) async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    await prefs.setString('dni', dni);
    await prefs.setString('codigo', codigo);
  }

  HttpClient getHttpClient() {
    HttpClient client = HttpClient();
    client.badCertificateCallback = (cert, host, port) => true;
    return client;
  }

  Future<void> iniciarSesion(BuildContext context) async {
    final String dni = dniController.text.trim();
    final String codigo = codigoController.text.trim();

    if (dni.isEmpty || codigo.isEmpty) {
      mostrarMensaje(context, "Por favor, complete ambos campos.");
      return;
    }

    try {
      final ioClient = IOClient(getHttpClient());
      final enlace = Uri.parse("https://chrononet.deltanetsi.es/api/usuarios/comprobar");

      final response = await ioClient.post(
        enlace,
        headers: {"Content-Type": "application/json"},
        body: json.encode({"dni": dni, "codigo": codigo}),
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = json.decode(response.body);

        if (data['success'] == true) {
          final Map<String, dynamic> userData = data['data'];

          // Guardar credenciales localmente
          await _saveCredentials(dni, codigo);

          // Convertir idUsuario a String explícitamente
          final int idUsuario = userData['idUsuario'];
          final String nombre = userData['nombre'] ?? '';
          final String apellidos = userData['apellidos'] ?? '';

          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder: (context) => FichajePage(
                idUsuario: idUsuario,
                nombre: nombre,
                apellidos: apellidos,
              ),
            ),
          );
        } else {
          mostrarMensaje(context, "Credenciales incorrectas.");
        }
      } else {
        mostrarMensaje(context, "Error del servidor: ${response.statusCode}");
      }
    } catch (e) {
      mostrarMensaje(context, "Error de conexión: ${e.toString()}");
    }
  }

  void mostrarMensaje(BuildContext context, String mensaje) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(mensaje), backgroundColor: Colors.red),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: SingleChildScrollView(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Logo
              Image.asset('assets/LogoColor.png', width: 194),
              const SizedBox(height: 20),

              // Campo DNI
              _buildTextField(dniController, "DNI (sin letra)", 8),
              const SizedBox(height: 20),

              // Campo Clave
              _buildTextField(codigoController, "Clave", 4),
              const SizedBox(height: 30),

              // Botón Aceptar
              ElevatedButton(
                onPressed: () => iniciarSesion(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF253439),
                  padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 60),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text(
                  'ACEPTAR',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String hint, int maxLength) {
    return Container(
      width: 300,
      child: TextField(
        controller: controller,
        maxLength: maxLength,
        keyboardType: TextInputType.number,
        textAlign: TextAlign.center,
        decoration: InputDecoration(
          counterText: '',
          hintText: hint,
          hintStyle: const TextStyle(color: Colors.grey, fontSize: 18),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(5),
            borderSide: const BorderSide(color: Color(0xFF253439), width: 2),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(5),
            borderSide: const BorderSide(color: Color(0xFF345A6E), width: 3),
          ),
        ),
        style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
      ),
    );
  }
}
