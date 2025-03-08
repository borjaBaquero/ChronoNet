using CL_CHRONO;
using DocumentFormat.OpenXml.Drawing.Charts;
using DocumentFormat.OpenXml.Office2016.Drawing.ChartDrawing;
using OfficeOpenXml;
using System;
using System.Collections.Generic;
using System.Data.SqlServerCe;

using System.Data.SqlClient;
using System.IO;
using System.Linq;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Threading;
using CL_CHRONO.Modelo;
using CL_CHRONO.Controlador;
using CL_CHRONO.Datos;
using Microsoft.Data.Sqlite;

namespace CL_CHRONO.Vista
{
    public partial class ListadoRegistros : Window
    {
        public DAOTRegistrados daoregistrados = new DAOTRegistrados();
        private DAOTUsuarios daousuarios;
        private DAOTRegistros daoregistros;
        private List<TUsuario> _usuarios;
        private TUsuario currentUser;
        private DispatcherTimer timer;

        public ListadoRegistros(TUsuario _currentUser)
        {
            try
            {
                InitializeComponent();
                currentUser = _currentUser;
                daoregistrados = new DAOTRegistrados();
                daoregistros = new DAOTRegistros();
                listarRegistrados(currentUser);
                

                // Inicializar el DAO de usuarios
                daousuarios = new DAOTUsuarios();

                LlenarComboBoxUsuarios(_currentUser);
                // Obtener la lista de usuarios desde el DAO
                _usuarios = daousuarios.ObtenerLista(_currentUser);

                // Modificar el ComboBox para mostrar el nombre completo
                cmbUsuarioNombreCompleto.ItemsSource = _usuarios;
                cmbUsuarioNombreCompleto.DisplayMemberPath = "NombreCompleto"; // Utiliza la propiedad NombreCompleto


                // Inicializa y configura el DispatcherTimer
                timer = new DispatcherTimer();
                timer.Interval = TimeSpan.FromSeconds(1);
                timer.Tick += Timer_Tick;

                // Inicia el DispatcherTimer
                timer.Start();
                // Configurar el formato de la columna de fecha en el DataGrid
                var fechaColumn = dataGrid.Columns.FirstOrDefault(c => c.SortMemberPath == "FechaHora") as DataGridTextColumn;
                if (fechaColumn != null)
                {
                    fechaColumn.Binding.StringFormat = "dd/MM/yy HH:mm:ss";
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
                MessageBox.Show($"Error al iniciar la página: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }


        //POSCIÓN CARGA DE WINDOW
        private void Window_Loaded(object sender, RoutedEventArgs e)
        {
            // Obtener el tamaño de la pantalla
            double screenWidth = SystemParameters.PrimaryScreenWidth;
            double screenHeight = SystemParameters.PrimaryScreenHeight;

            // Establecer la posición de la ventana en el centro de la pantalla
            Left = (screenWidth - Width) / 2;
            Top = (screenHeight - Height) / 2;
        }


    private void listarRegistrados(TUsuario us)
        {
            try
            {
                // Obtener valores de los controles de filtro
                DateTime fechaInicio = datePickerFechaInicio.SelectedDate ?? DateTime.MinValue;
                DateTime fechaFin = datePickerFechaFin.SelectedDate ?? DateTime.MaxValue;

                // Obtener el listado completo
                List<TRegistrado> listadoCompleto = daoregistrados.ObtenerLista(us);

                // Filtrar por fecha
                List<TRegistrado> listadoFiltrado = listadoCompleto.ToList();

                // Ordenar la lista filtrada por fecha descendente
                listadoFiltrado = listadoFiltrado.OrderByDescending(registrado => registrado.FechaHora).ToList();

                // Vincula la lista filtrada al ItemsSource del DataGrid
                dataGrid.ItemsSource = listadoFiltrado;
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
                MessageBox.Show($"Error al obtener la lista de registros: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void Timer_Tick(object sender, EventArgs e)
        {
            try
            {
                txtReloj.Text = DateTime.Now.ToString("HH:mm:ss");
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
            }
        }

        private void Click_btnSalirPantalla(object sender, RoutedEventArgs e)
        {
            try
            {
                Fichaje vent = new Fichaje(currentUser);
                vent.Show();
                CL_CHRONO.Logger.LogMessage("Salida de la página realizada con éxito");
                this.Close();
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
                MessageBox.Show($"Error al salir de la página: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void Click_btnDescargaListado(object sender, RoutedEventArgs e)
        {
            try
            {
                ExcelPackage.LicenseContext = LicenseContext.NonCommercial;

                string carpetaDescargas = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.UserProfile), "Downloads");

                if (!Directory.Exists(carpetaDescargas))
                {
                    Directory.CreateDirectory(carpetaDescargas);
                }

                string fechaActual = DateTime.Now.ToString("yyyyMMdd");

                DateTime fechaInicio = datePickerFechaInicio.SelectedDate ?? DateTime.MinValue;
                DateTime fechaFin = datePickerFechaFin.SelectedDate ?? DateTime.MaxValue;
                string nombreFiltro = cmbUsuarioNombreCompleto.Text;
                string incidenciaFiltro = (cmbFiltroIncidencia.SelectedItem as ComboBoxItem)?.Content.ToString();

                string nombreArchivo = $"registros_{fechaActual}.xlsx";
                string rutaCompleta = Path.Combine(carpetaDescargas, nombreArchivo);

                if (File.Exists(rutaCompleta))
                {
                    File.Delete(rutaCompleta);
                }

                FileInfo excelFile = new FileInfo(rutaCompleta);
                using (ExcelPackage excelPackage = new ExcelPackage(excelFile))
                {
                    ExcelWorksheet worksheet = excelPackage.Workbook.Worksheets.Add("Registros");

                    worksheet.Cells["A1"].Value = "Nombre Completo";
                    worksheet.Cells["B1"].Value = "ID Registro";
                    worksheet.Cells["C1"].Value = "ID Usuario";
                    worksheet.Cells["D1"].Value = "Tipo";
                    worksheet.Cells["E1"].Value = "Fecha y Hora";
                    worksheet.Cells["F1"].Value = "Incidencia";
                    worksheet.Cells["G1"].Value = "Horas Trabajadas";

                    string sqlQuery = @"
                SELECT tr.idRegistro, tr.idUsuario, tr.Tipo, tr.FechaHora, tr.Incidencia, 
                       (tu.Nombre || ' ' || tu.Apellidos) AS NombreCompleto
                FROM tregistro tr 
                LEFT JOIN tusuario tu ON tu.idUsuario = tr.idUsuario
                WHERE DATE(tr.FechaHora) >= @FechaInicio 
                  AND DATE(tr.FechaHora) <= @FechaFin";

                    if (!string.IsNullOrEmpty(nombreFiltro))
                    {
                        sqlQuery += " AND (tu.Nombre || ' ' || tu.Apellidos) LIKE @NombreFiltro";
                    }

                    if (!string.IsNullOrEmpty(incidenciaFiltro) && incidenciaFiltro != "Todos")
                    {
                        sqlQuery += " AND tr.Incidencia = @IncidenciaFiltro";
                    }

                    sqlQuery += " ORDER BY tr.FechaHora DESC";

                    List<TRegistrado> results = new List<TRegistrado>();

                    using (DBConnection conn = new DBConnection())
                    {
                        using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                        {
                            cmd.Parameters.AddWithValue("@FechaInicio", fechaInicio.ToString("yyyy-MM-dd"));
                            cmd.Parameters.AddWithValue("@FechaFin", fechaFin.ToString("yyyy-MM-dd"));

                            if (!string.IsNullOrEmpty(nombreFiltro))
                            {
                                cmd.Parameters.AddWithValue("@NombreFiltro", $"%{nombreFiltro}%");
                            }

                            if (!string.IsNullOrEmpty(incidenciaFiltro) && incidenciaFiltro != "Todos")
                            {
                                cmd.Parameters.AddWithValue("@IncidenciaFiltro", incidenciaFiltro);
                            }

                            using (var reader = cmd.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    results.Add(new TRegistrado
                                    {
                                        Nombre = reader.GetString(reader.GetOrdinal("NombreCompleto")),
                                        idRegistro = reader.GetInt32(reader.GetOrdinal("idRegistro")),
                                        idUsuario = reader.GetInt32(reader.GetOrdinal("idUsuario")),
                                        Tipo = ObtenerTipoTexto(reader.GetInt16(reader.GetOrdinal("Tipo"))),
                                        FechaHora = reader.GetDateTime(reader.GetOrdinal("FechaHora")),
                                        Incidencia = reader.GetString(reader.GetOrdinal("Incidencia"))
                                    });
                                }
                            }
                        }
                    }

                    // Procesar y exportar los datos
                    int fila = 2;
                    foreach (var registro in results)
                    {
                        worksheet.Cells[$"A{fila}"].Value = registro.Nombre;
                        worksheet.Cells[$"B{fila}"].Value = registro.idRegistro;
                        worksheet.Cells[$"C{fila}"].Value = registro.idUsuario;
                        worksheet.Cells[$"D{fila}"].Value = registro.Tipo;
                        worksheet.Cells[$"E{fila}"].Value = registro.FechaHora.ToString("dd/MM/yyyy HH:mm:ss");
                        worksheet.Cells[$"F{fila}"].Value = registro.Incidencia;

                        fila++;
                    }

                    excelPackage.Save();
                }

                MessageBox.Show($"Archivo Excel generado exitosamente en la ruta {rutaCompleta}", "Éxito", MessageBoxButton.OK, MessageBoxImage.Information);
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Error al generar el archivo Excel: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private string ObtenerTipoTexto(int tipo)
        {
            return tipo switch
            {
                0 => "Entrada",
                1 => "Salida",
                2 => "Pausa",
                3 => "Reanudar",
                _ => "Desconocido"
            };
        }
        private void Click_btnFiltros(object sender, RoutedEventArgs e)
        {
            // Cambia la visibilidad del Popup de filtros
            popupFiltros.IsOpen = !popupFiltros.IsOpen;
        }

        private void Click_btnResetFiltros(object sender, RoutedEventArgs e)
        {
            try
            {
                // Limpiar controles de filtro
                datePickerFechaInicio.SelectedDate = null;
                datePickerFechaFin.SelectedDate = null;
                cmbUsuarioNombreCompleto.Text = "";
                cmbFiltroIncidencia.SelectedIndex = 0; // Establecer la selección por defecto (Todos)

                // Restablecer el listado principal
                listarRegistrados(currentUser);

                // Ocultar el Popup de Filtros
                popupFiltros.IsOpen = false;

                CL_CHRONO.Logger.LogMessage("Filtros restablecidos.");
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
                MessageBox.Show($"Error al restablecer los filtros: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void Click_btnAplicarFiltros(object sender, RoutedEventArgs e)
        {
            try
            {
                // Obtener valores de los controles de filtro
                string nombreFiltro = cmbUsuarioNombreCompleto.Text.ToLower();  // Convertir a minúsculas
                string incidenciaFiltro = (cmbFiltroIncidencia.SelectedItem as ComboBoxItem)?.Content.ToString().ToLower();  // Convertir a minúsculas

                // Obtener el rango de fechas seleccionado
                DateTime fechaInicio = datePickerFechaInicio.SelectedDate ?? DateTime.MinValue;
                DateTime fechaFin = datePickerFechaFin.SelectedDate ?? DateTime.MaxValue;

                // Obtener el listado completo
                List<TRegistrado> listadoCompleto = daoregistrados.ObtenerLista(currentUser);

                // Aplicar los filtros
                List<TRegistrado> listadoFiltrado = listadoCompleto
                    .Where(registrado =>
                        (registrado.FechaHora.Date >= fechaInicio.Date && registrado.FechaHora.Date <= fechaFin.Date) &&
                        (string.IsNullOrEmpty(nombreFiltro) || (registrado.Nombre + " " + registrado.Apellidos).ToLower().Contains(nombreFiltro)) && // Concatenar Nombre y Apellidos
                        (string.IsNullOrEmpty(incidenciaFiltro) || incidenciaFiltro == "todos" || registrado.Incidencia.ToLower() == incidenciaFiltro))
                    .ToList();

                // Vincular el listado filtrado al ItemsSource del DataGrid
                dataGrid.ItemsSource = listadoFiltrado;

                CL_CHRONO.Logger.LogMessage("Filtros aplicados con éxito");
            }
            catch (Exception ex)
            {
                // Manejo de errores: muestra un mensaje, registra el error, etc.
                CL_CHRONO.Logger.LogException("ERROR:", ex);
                MessageBox.Show($"Error al aplicar los filtros: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
            finally
            {
                // Ocultar el Popup de Filtros
                popupFiltros.IsOpen = false;
            }
        }

        private void LlenarComboBoxUsuarios(TUsuario act)
        {
            comboUsuarios.Items.Clear();
            List<TUsuario> usuarios = daousuarios.ObtenerLista(act);

            foreach (TUsuario usuario in usuarios)
            {
                ComboBoxItem item = new ComboBoxItem()
                {
                    Content = $"{usuario.Nombre} {usuario.Apellidos}",
                    Tag = usuario.idUsuario
                };
                comboUsuarios.Items.Add(item);
            }
        }

        private void Guardar_Click(object sender, RoutedEventArgs e)
        {
            // Lógica para guardar el registro
            ComboBoxItem selectedUser = (ComboBoxItem)comboUsuarios.SelectedItem;
            if (selectedUser == null)
            {
                MessageBox.Show("Por favor, selecciona un usuario.");
                return;
            }

            int usuarioId = (int)selectedUser.Tag;
            DateTime? fechaSeleccionada = datePicker.SelectedDate;
            string hora = txtHora.Text;
            bool incidencia = checkIncidencia.IsChecked == true;
            ComboBoxItem selectedTipo = (ComboBoxItem)comboTipo.SelectedItem;

            if (fechaSeleccionada == null)
            {
                MessageBox.Show("Por favor, selecciona una fecha.");
                return;
            }

            if (string.IsNullOrEmpty(hora))
            {
                MessageBox.Show("Por favor, ingresa la hora.");
                return;
            }

            if (selectedTipo == null)
            {
                MessageBox.Show("Por favor, selecciona un tipo.");
                return;
            }

            DateTime fechaHora;
            if (!DateTime.TryParse($"{fechaSeleccionada.Value:yyyy-MM-dd} {hora}", out fechaHora))
            {
                MessageBox.Show("La fecha y hora no tienen un formato válido.");
                return;
            }

            short tipo = short.Parse(selectedTipo.Tag.ToString());

            TRegistro nuevoRegistro = new TRegistro()
            {
                idUsuario = usuarioId,
                Tipo = tipo,
                FechaHora = fechaHora,
                Incidencia = incidencia ? "Sí" : "No"
            };

            bool registroInsertado = daoregistros.InsertarRegistro(nuevoRegistro);
            if (registroInsertado)
            {
                MessageBox.Show("Registro guardado exitosamente.");
            }
            else
            {
                MessageBox.Show("Hubo un error al guardar el registro.");
            }

            // Cerrar el Popup
            popupFormulario.IsOpen = false;
        }

        private void Cancelar_Click(object sender, RoutedEventArgs e)
        {
            // Cerrar el Popup sin guardar
            popupFormulario.IsOpen = false;
            
        }

        private void dataGrid_PreviewMouseDoubleClick(object sender, System.Windows.Input.MouseButtonEventArgs e)
        {
            e.Handled = true;
        }

        private void dataGrid_SelectedCellsChanged(object sender, SelectedCellsChangedEventArgs e)
        {
            dataGrid.SelectedItem = null;
        }
    }

}