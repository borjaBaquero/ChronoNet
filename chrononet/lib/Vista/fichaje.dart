import 'package:flutter/material.dart';
import 'dart:async';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'listadoregistros.dart'; // Importa la página de listado de registros

class FichajePage extends StatefulWidget {
  final int idUsuario;
  final String nombre;
  final String apellidos;

  const FichajePage({
    super.key,
    required this.idUsuario,
    required this.nombre,
    required this.apellidos,
  });

  @override
  _FichajePageState createState() => _FichajePageState();
}

class _FichajePageState extends State<FichajePage> {
  late Timer _timer;
  String _currentTime = "";

  @override
  void initState() {
    super.initState();
    _updateTime();
    _timer = Timer.periodic(const Duration(seconds: 1), (Timer t) => _updateTime());
  }

  void _updateTime() {
    final now = DateTime.now();
    setState(() {
      _currentTime =
          "${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}:${now.second.toString().padLeft(2, '0')}";
    });
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  // Función para registrar entrada o salida
  Future<void> registrarFichaje(int tipo) async {
    final url = Uri.parse("https://chrononet.deltanetsi.es/api/fichajes/registrarDesdeApp");

    try {
      final response = await http.post(
        url,
        headers: {"Content-Type": "application/json"},
        body: json.encode({"idUsuario": widget.idUsuario, "tipo": tipo}),
      );

      final responseData = json.decode(response.body);
      if (response.statusCode == 200 && responseData['success']) {
        _showMessage(responseData['message']);
        Future.delayed(const Duration(seconds: 2), (){
          Navigator.popUntil((context), ModalRoute.withName(Navigator.defaultRouteName));
        });
      } else {
        _showMessage(responseData['message'] ?? "Error al registrar el fichaje.");
      }
    } catch (e) {
      _showMessage("Error de conexión: ${e.toString()}");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () => Navigator.pop(context),
        ),
        title: Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            const Text(
              "Fichaje - ",
              style: TextStyle(
                color: Color(0xFF253439),
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            Text(
              "${widget.nombre} ${widget.apellidos}",
              style: const TextStyle(
                color: Color(0xFF253439),
                fontSize: 20,
                fontWeight: FontWeight.normal,
              ),
            ),
          ],
        ),
      ),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            buildButton("MIS REGISTROS", () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => ListadoRegistrosPage(idUsuario: widget.idUsuario),
                ),
              );
            }),
            const SizedBox(height: 15),
            buildButton("ENTRADA", () => registrarFichaje(0)), // Tipo 0 = Entrada
            const SizedBox(height: 15),
            buildButton("SALIDA", () => registrarFichaje(1)), // Tipo 1 = Salida
            const SizedBox(height: 50),

            // Reloj dinámico
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              decoration: BoxDecoration(
                color: const Color(0xFF253439),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                _currentTime,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
            const SizedBox(height: 5),
            const Text(
              "DNET-RRHH",
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: Color(0xFF8C8C8C),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget buildButton(String text, VoidCallback onPressed) {
    return SizedBox(
      width: 250,
      height: 50,
      child: ElevatedButton(
        onPressed: onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color(0xFF253439),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
        ),
        child: Text(
          text,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.green,
      ),
    );
  }
}
