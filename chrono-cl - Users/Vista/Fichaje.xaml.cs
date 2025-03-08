using System;
using System.Collections.Generic;
using System.Linq;
using System.Data.SqlServerCe;

using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;
using System.Windows.Threading;
using CL_CHRONO.Modelo;
using CL_CHRONO.Controlador;
using System.Net.Http;
using System.Threading;

namespace CL_CHRONO.Vista
{
    /// <summary>
    /// Lógica de interacción para Fichaje.xaml
    /// </summary>
    public partial class Fichaje : Window
    {
        private readonly TUsuario _currentUser;
        private CancellationTokenSource _cts = new CancellationTokenSource();
        private DispatcherTimer timer;
        private bool haSalido = false;
        private bool haEntrado = false;
        private bool haReanudado = false;
        private bool haPausado;

        private static readonly HttpClient client = new HttpClient(new HttpClientHandler
        {
            ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => true
        })
        {
            Timeout = TimeSpan.FromSeconds(5) // Definir el timeout solo una vez
        };

        public Fichaje(TUsuario usuario)
        {
            try
            {
                InitializeComponent();
                _currentUser = usuario;
                Title += " - " + usuario.NombreCompleto;

                // Mostrar botón PAUSA/REANUDAR dependiendo de si se Pauso en algún momento o no
                DAOTRegistros daoTRegistros = new();
                TRegistro? ultimoRegistro = daoTRegistros.ObtenerUltimoRegistroDelDia(_currentUser.idUsuario);

                if (ultimoRegistro != null)
                {
                    switch (ultimoRegistro.Tipo)
                    {
                        case 0:  // ENTRADA
                            haEntrado = true;
                            haSalido = false;
                            haPausado = false;
                            haReanudado = false;
                            break;
                        case 1:  // SALIDA
                            haEntrado = false;
                            haSalido = true;
                            haPausado = false;
                            haReanudado = false;
                            break;
                        case 2:  // PAUSA
                            haEntrado = true;
                            haSalido = false;
                            haPausado = true;
                            haReanudado = false;
                            btnPausaFichaje.Visibility = Visibility.Hidden;
                            btnReanudarFichaje.Visibility = Visibility.Visible;
                            break;
                        case 3:  // REANUDAR
                            haEntrado = true;
                            haSalido = false;
                            haPausado = false;
                            haReanudado = true;
                            break;
                    }
                }
                else
                {
                    // Si no hay registro, entonces el usuario no ha entrado ni salido
                    haEntrado = false;
                    haSalido = false;
                    haPausado = false;
                    haReanudado = false;
                }


                // Inicializa y configura el DispatcherTimer
                timer = new DispatcherTimer();
                timer.Interval = TimeSpan.FromSeconds(1);
                timer.Tick += Timer_Tick;

                // Inicia el DispatcherTimer
                timer.Start();
                IniciarComprobacionConexion();

            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
                MessageBox.Show("Ha ocurrido un error al iniciar la página. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
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


        //SALIR A LA WINDOW ANTERIOR
        private void CerrarApp_Click(object sender, RoutedEventArgs e)
        {
            Inicio inicio = new Inicio();
            inicio.Show();
            this.Close();
        }


        //RELOJ
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


        //ENTRADA - SALIDA - PAUSA - REANUDAR
        private async void Click_btnEntradaFichaje(object sender, RoutedEventArgs e)
        {
            try
            {
                if (!haEntrado || haSalido)
                {
                    RealizarRegistro(0, "ENTRADA");
                    CL_CHRONO.Logger.LogMessage(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - Entrada realizada con éxito");
                    haEntrado = true; // Actualizamos el estado después de la entrada exitosa
                    await SincronizarRegistrosNoSincronizados();

                }
                else
                {
                    MessageBox.Show("Ya ha realizado una entrada hoy o necesita realizar una salida antes de entrar de nuevo.", "Advertencia", MessageBoxButton.OK, MessageBoxImage.Warning);
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - ERROR: ", ex);
                MessageBox.Show("Ha ocurrido un error al realizar la entrada. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }
        private async void Click_btnSalidaFichaje(object sender, RoutedEventArgs e)
        {
            try
            {
                if ((haEntrado && !haSalido))
                {
                    if (haPausado) // Si ha pausado, generamos el registro de reanudar antes de la salida
                    {
                        // Comprobar si el último registro es una pausa antes de generar la reanudación
                        DAOTRegistros daoTRegistros = new DAOTRegistros();
                        TRegistro? ultimoRegistro = daoTRegistros.ObtenerUltimoRegistroDelDia(_currentUser.idUsuario);
                        if (ultimoRegistro != null && ultimoRegistro.Tipo == 2) // Si el último registro es una pausa
                        {
                            RealizarRegistroSinSalida(3, "REANUDAR");
                            CL_CHRONO.Logger.LogMessage(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - Reanudación realizada automáticamente antes de la salida");

                        }
                    }
                    // Crear y verificar el registro de salida con tipo `1`
                    TRegistro salidaRegistro = new TRegistro
                    {
                        Tipo = 1,  // Asegúrate de que el tipo sea `1`
                        FechaHora = DateTime.Now,
                        idUsuario = _currentUser.idUsuario,
                        Incidencia = "NO",
                        Recogido = 0
                    };
                    // Llama al método de inserción con este registro `Tipo = 1`
                    DAOTRegistros daoTRegistross = new DAOTRegistros();
                    bool result = daoTRegistross.InsertarRegistro(salidaRegistro);

                    if (result)
                    {                        
                        MessageBox.Show($"Fichaje salida realizado con éxito", "OK", MessageBoxButton.OK, MessageBoxImage.Information);
                        await SincronizarRegistrosNoSincronizados();
                    }


                    Inicio inicio = new Inicio();
                    inicio.Show();
                    this.Close(); ;
                    // No restablecer las variables aquí
                    // haEntrado = false;
                    // haSalido = true;
                    // haReanudado = false;
                    // haPausado = false;
                }
                else
                {
                    MessageBox.Show("No puede realizar una salida sin haber entrado o después de haber salido.", "Advertencia", MessageBoxButton.OK, MessageBoxImage.Warning);
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - ERROR: ", ex);
                MessageBox.Show("Ha ocurrido un error al realizar la salida. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }
        private async void Click_btnPausar(object sender, RoutedEventArgs e)
        {
            try
            {
                if (haEntrado && !haSalido)
                {
                    RealizarRegistro(2, "PAUSA");
                    CL_CHRONO.Logger.LogMessage(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - Pausa realizada con éxito");
                    haPausado = true; // Actualizamos el estado después de la pausa exitosa

                    await SincronizarRegistrosNoSincronizados();

                }
                else
                {
                    MessageBox.Show("No puede pausar sin haber entrado o después de haber salido.", "Advertencia", MessageBoxButton.OK, MessageBoxImage.Warning);
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - ERROR: ", ex);
                MessageBox.Show("Ha ocurrido un error al realizar la pausa. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }
        private async void Click_btnReanudar(object sender, RoutedEventArgs e)
        {
            try
            {
                if (haEntrado && !haSalido && haPausado)
                {
                    RealizarRegistro(3, "REANUDAR");
                    CL_CHRONO.Logger.LogMessage(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - Reanudación realizada con éxito");
                    haPausado = false; // Actualizamos el estado después de la reanudación exitosa
                    haReanudado = true; // Actualizamos el estado después de la reanudación exitosa

                    await SincronizarRegistrosNoSincronizados();

                }
                else
                {
                    MessageBox.Show("No puede reanudar sin haber entrado, después de haber salido o sin haber pausado.", "Advertencia", MessageBoxButton.OK, MessageBoxImage.Warning);
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - ERROR: ", ex);
                MessageBox.Show("Ha ocurrido un error al realizar la reanudación. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }


        //COMPROBACIONES
        private void RealizarRegistro(short tipoRegistro, string mensaje)
        {
            try
            {
                DAOTRegistros daoTRegistros = new();
                string incidencia = tipoRegistro == 2 ? "SI" : "NO";

                TRegistro newRegistro = new()
                {
                    idUsuario = _currentUser.idUsuario,
                    Tipo = tipoRegistro,
                    FechaHora = DateTime.Now,
                    Incidencia = incidencia,
                    Recogido = 0
                };

                bool result = daoTRegistros.InsertarRegistro(newRegistro);

                if (result)
                {
                    Task.Run(() => SincronizarRegistrosNoSincronizados());
                    MessageBox.Show($"Fichaje {mensaje} realizado con éxito", "OK", MessageBoxButton.OK, MessageBoxImage.Information);
                }

                Inicio inicio = new Inicio();
                inicio.Show();
                this.Close(); ;
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - ERROR: ", ex);
                MessageBox.Show($"Ha ocurrido un error al realizar el registro. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void RealizarRegistroSinSalida(short tipoRegistro, string mensaje)
        {
            try
            {
                DAOTRegistros daoTRegistros = new();
                string incidencia = tipoRegistro == 2 ? "SI" : "NO";

                TRegistro newRegistro = new()
                {
                    idUsuario = _currentUser.idUsuario,
                    Tipo = tipoRegistro,
                    FechaHora = DateTime.Now,
                    Incidencia = incidencia,
                    Recogido = 0
                };

                bool result = daoTRegistros.InsertarRegistro(newRegistro);

                if (result)
                {
                    MessageBox.Show($"Fichaje {mensaje} realizado con éxito", "OK", MessageBoxButton.OK, MessageBoxImage.Information);
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException(_currentUser.idUsuario + " - " + _currentUser.NombreCompleto + " - ERROR: ", ex);
                MessageBox.Show($"Ha ocurrido un error al realizar el registro. Consulte los registros para más información.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private async Task SincronizarRegistrosNoSincronizados()
        {
            try
            {
                if (await ServidorDisponible())
                {
                    DAOTRegistros daoTRegistros = new();
                    await daoTRegistros.SincronizarRegistrosPendientes();
                }
            }
            catch (Exception ex)
            {
               MessageBox.Show($"Error al sincronizar: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        public async Task<bool> ServidorDisponible()
        {
            try
            {
                using (var localHandler = new HttpClientHandler
                {
                    ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => true
                })
                using (var localClient = new HttpClient(localHandler))
                {
                    var response = await localClient.GetAsync("https://chrononet.deltanetsi.es/api/ping");
                    return response.IsSuccessStatusCode;
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR: ", ex);
                return false;
            }
        }
        private void btnVerRegistros_Click(object sender, RoutedEventArgs e)
        {
            ListadoRegistros list = new ListadoRegistros(_currentUser);
            this.Close();
            list.Show();
        }


        private void IniciarComprobacionConexion()
        {
            Task.Run(async () =>
            {
                while (!_cts.Token.IsCancellationRequested)
                {
                    try
                    {
                        bool servidorOnline = await ServidorDisponible();

                        if (servidorOnline)
                        {
                            DAOTRegistros daoTRegistros = new();
                            var registrosPendientes = daoTRegistros.ObtenerRegistrosNoSincronizados();

                            if (registrosPendientes.Any())
                            {
                                Console.WriteLine($"Conexión disponible. Sincronizando {registrosPendientes.Count} registros...");
                                await daoTRegistros.SincronizarRegistrosPendientes();
                                Console.WriteLine("Sincronización completada.");
                            }
                        }
                        else
                        {
                            Console.WriteLine("Sin conexión. Reintentando en 5 minutos...");
                        }
                    }
                    catch (Exception ex)
                    {
                        CL_CHRONO.Logger.LogException("Error en la comprobación de conexión: ", ex);
                    }

                    // Esperar 5 minutos antes del próximo intento
                    await Task.Delay(TimeSpan.FromMinutes(3), _cts.Token);
                }
            }, _cts.Token);
        }

        // Llamar a este método cuando cierres la ventana para detener el hilo
        private void DetenerComprobacionConexion()
        {
            _cts.Cancel();
        }
    }
}
