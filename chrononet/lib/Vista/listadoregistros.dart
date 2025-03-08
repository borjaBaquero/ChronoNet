import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class ListadoRegistrosPage extends StatefulWidget {
  final int idUsuario;

  const ListadoRegistrosPage({super.key, required this.idUsuario});

  @override
  _ListadoRegistrosPageState createState() => _ListadoRegistrosPageState();
}

class _ListadoRegistrosPageState extends State<ListadoRegistrosPage> {
  List registros = [];
  List registrosFiltrados = [];
  bool isLoading = true;

  // Filtros
  DateTime? fechaInicio;
  DateTime? fechaFin;
  String? tipoSeleccionado;
  String? incidenciaSeleccionada;

  final List<String> tipos = ["Entrada", "Salida", "Pausa", "Reanudar"];
  final List<String> incidencias = ["SI", "NO"];

  @override
  void initState() {
    super.initState();
    fetchRegistros();
  }

  /// Función para obtener los registros del servidor
  Future<void> fetchRegistros() async {
    setState(() => isLoading = true);

    try {
      final response = await http.post(
        Uri.parse("https://chrononet.deltanetsi.es/api/usuarios/listar"),
        headers: {"Content-Type": "application/json"},
        body: json.encode({"idUsuario": widget.idUsuario}),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success']) {
          setState(() {
            registros = data['data'];

            // Filtrar inicialmente solo los registros de los últimos 30 días
            DateTime hoy = DateTime.now();
            DateTime hace30Dias = hoy.subtract(const Duration(days: 30));

            registrosFiltrados = registros.where((registro) {
              final fechaRegistro = DateTime.parse(registro['FechaHora']);
              return fechaRegistro.isAfter(hace30Dias) &&
                  fechaRegistro.isBefore(hoy);
            }).toList();
          });
        } else {
          _showMessage(data['message']);
        }
      } else {
        _showMessage("Error del servidor");
      }
    } catch (e) {
      _showMessage("Error de conexión: ${e.toString()}");
    }

    setState(() => isLoading = false);
  }

  /// Función para aplicar los filtros
  void _applyFilters() {
    setState(() {
      registrosFiltrados = registros.where((registro) {
        final fechaRegistro = DateTime.parse(registro['FechaHora']);
        final tipo = obtenerTipoDescripcion(
            int.tryParse(registro['Tipo'].toString()) ?? -1);
        final incidencia = registro['Incidencia']?.toString() ?? "";

        // Filtro por Fecha
        bool cumpleFecha = true;
        if (fechaInicio != null) cumpleFecha &= fechaRegistro.isAfter(fechaInicio!);
        if (fechaFin != null) cumpleFecha &= fechaRegistro.isBefore(fechaFin!);

        // Filtro por Tipo
        bool cumpleTipo = tipoSeleccionado == null || tipo == tipoSeleccionado;

        // Filtro por Incidencia
        bool cumpleIncidencia = incidenciaSeleccionada == null ||
            incidencia.toLowerCase() == incidenciaSeleccionada?.toLowerCase();

        return cumpleFecha && cumpleTipo && cumpleIncidencia;
      }).toList();
    });
  }

  /// Función para resetear los filtros
  void _resetFilters() {
    setState(() {
      fechaInicio = null;
      fechaFin = null;
      tipoSeleccionado = null;
      incidenciaSeleccionada = null;

      // Volvemos a mostrar solo los últimos 30 días al resetear
      DateTime hoy = DateTime.now();
      DateTime hace30Dias = hoy.subtract(const Duration(days: 30));
      registrosFiltrados = registros.where((registro) {
        final fechaRegistro = DateTime.parse(registro['FechaHora']);
        return fechaRegistro.isAfter(hace30Dias) &&
            fechaRegistro.isBefore(hoy);
      }).toList();
    });
  }

  /// Función para mostrar mensajes
  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  /// Función para obtener la descripción del tipo de registro
  String obtenerTipoDescripcion(int tipo) {
    switch (tipo) {
      case 0:
        return "Entrada";
      case 1:
        return "Salida";
      case 2:
        return "Pausa";
      case 3:
        return "Reanudar";
      default:
        return "Desconocido";
    }
  }

  Future<void> _selectDate(Function(DateTime) onDateSelected) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2000),
      lastDate: DateTime(2100),
    );
    if (picked != null) onDateSelected(picked);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Listado de Registros")),
      body: Column(
        children: [
          // Filtros
          _buildFilterSection(),

          // Tabla de registros
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : SingleChildScrollView(
                    scrollDirection: Axis.vertical,
                    child: DataTable(
                      columnSpacing: 20,
                      columns: const [
                        DataColumn(label: Text("Tipo")),
                        DataColumn(label: Text("FechaHora")),
                        DataColumn(label: Text("Incidencia")),
                        DataColumn(label: Text("TotalHoras")),
                      ],
                      rows: registrosFiltrados.map((registro) {
                        return DataRow(cells: [
                          DataCell(Text(obtenerTipoDescripcion(
                              int.tryParse(registro['Tipo'].toString()) ?? -1))),
                          DataCell(Text(registro['FechaHora'].toString())),
                          DataCell(Text(registro['Incidencia'].toString())),
                          DataCell(Text(
                              registro['TotalHoras']?.toString() ?? "0.00")),
                        ]);
                      }).toList(),
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterSection() {
    return Card(
      margin: const EdgeInsets.all(10),
      child: Padding(
        padding: const EdgeInsets.all(8.0),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildDateField("Fecha Inicio", fechaInicio, (date) {
                  setState(() => fechaInicio = date);
                }),
                _buildDateField("Fecha Fin", fechaFin, (date) {
                  setState(() => fechaFin = date);
                }),
              ],
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildDropdown("Tipo", tipos, tipoSeleccionado, (value) {
                  setState(() => tipoSeleccionado = value);
                }),
                _buildDropdown("Incidencia", incidencias, incidenciaSeleccionada,
                    (value) {
                  setState(() => incidenciaSeleccionada = value);
                }),
              ],
            ),
            const SizedBox(height: 10),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildStyledButton("APLICAR", _applyFilters),
                _buildStyledButton("RESET", _resetFilters),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDateField(String label, DateTime? date, Function(DateTime) onDateSelected) {
    return GestureDetector(
      onTap: () => _selectDate(onDateSelected),
      child: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Text(date == null ? label : "${date.toLocal()}".split(' ')[0]),
      ),
    );
  }

  Widget _buildDropdown(String label, List<String> items, String? selectedValue, Function(String?) onChanged) {
    return DropdownButton<String>(
      hint: Text(label),
      value: selectedValue,
      onChanged: onChanged,
      items: items.map((item) => DropdownMenuItem(value: item, child: Text(item))).toList(),
    );
  }

  Widget _buildStyledButton(String text, VoidCallback onPressed) {
    return SizedBox(
      width: 130,
      height: 50,
      child: ElevatedButton(
        onPressed: onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color(0xFF253439),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        ),
        child: Text(text, style: const TextStyle(color: Colors.white, fontSize: 16)),
      ),
    );
  }
}
