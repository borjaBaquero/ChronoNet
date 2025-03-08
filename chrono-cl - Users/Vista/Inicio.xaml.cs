using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Net.NetworkInformation;
using System.Net.Sockets;
using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;
using System.Data.SqlServerCe;

using System.Windows.Media.Animation;
using CL_CHRONO.Controlador;
using CL_CHRONO.Modelo;
namespace CL_CHRONO.Vista
{
    public partial class Inicio : Window
    {
        private System.Windows.Forms.NotifyIcon notifyIcon;
        public Inicio()
        {
            InitializeComponent();


            // Inicializa el NotifyIcon
            notifyIcon = new System.Windows.Forms.NotifyIcon
            {
                Icon = System.Drawing.Icon.ExtractAssociatedIcon(System.Reflection.Assembly.GetExecutingAssembly().Location),
                Visible = false,
                Text = "Chrono está ejecutándose"
            };

            // Evento al hacer doble clic en el icono
            notifyIcon.DoubleClick += (s, e) =>
            {
                Show(); // Muestra la ventana
                WindowState = WindowState.Normal; // Restaura el estado de la ventana
                notifyIcon.Visible = false; // Oculta el icono de la bandeja
            };
            ConfigureNotifyIcon();

            // Cargar el último usuario al iniciar la ventana
            tbDNI.Text = AppSettings.Default.LastUserDNI;

            //Llamo a función de la dirección IP
            Loaded += (sender, e) => ObtenerDireccionIP();
        }
        protected override void OnStateChanged(EventArgs e)
        {
            base.OnStateChanged(e);

            if (WindowState == WindowState.Minimized)
            {
                Hide(); // Oculta la ventana
                notifyIcon.Visible = true; // Muestra el icono en la bandeja
            }
        }

        protected override void OnClosing(System.ComponentModel.CancelEventArgs e)
        {
            base.OnClosing(e);
            e.Cancel = true; // Cancela el cierre de la aplicación
            Hide(); // Oculta la ventana
            notifyIcon.Visible = true; // Muestra el icono en la bandeja
        }

        private void ConfigureNotifyIcon()
        {
            var contextMenu = new System.Windows.Forms.ContextMenuStrip();
            var exitMenuItem = new System.Windows.Forms.ToolStripMenuItem("Salir");
            exitMenuItem.Click += (s, e) =>
            {
                notifyIcon.Visible = false; // Oculta el icono de la bandeja
                Application.Current.Shutdown(); // Cierra la aplicación
            };
            contextMenu.Items.Add(exitMenuItem);
            notifyIcon.ContextMenuStrip = contextMenu;
        }


        private void Window_Loaded(object sender, RoutedEventArgs e)
        {
            // Obtener el tamaño de la pantalla
            double screenWidth = SystemParameters.PrimaryScreenWidth;
            double screenHeight = SystemParameters.PrimaryScreenHeight;

            // Establecer la posición de la ventana en el centro de la pantalla
            Left = (screenWidth - Width) / 2;
            Top = (screenHeight - Height) / 2;

            // Cargar el último usuario y su código
            tbDNI.Text = AppSettings.Default.LastUserDNI;
            tbCodigo.Text = AppSettings.Default.LastUserCode.ToString();
        }


        //INICIAR SESIÓN
        private void Click_btnAceptar(object sender, RoutedEventArgs e)
        {
            bool isNumber = Int32.TryParse(tbCodigo.Text, out int codigo);
            if (!isNumber)
            {
                MostrarCodigoIncorrecto();
                tbCodigo.Focus();
                return;
            }

            DAOTUsuarios daoTUsuarios = new();
            List<TUsuario> usuarios = daoTUsuarios.ObtenerUsuariosPorCodigo(codigo);

            TUsuario? usuario = usuarios.FirstOrDefault(u => u.DNI == tbDNI.Text);

            if (usuario != null)
            {
                // Guardar el último usuario y su código
                AppSettings.Default.LastUserDNI = usuario.DNI;
                AppSettings.Default.LastUserCode = codigo;
                AppSettings.Default.Save();

                // Abre la ventana de fichaje
                Fichaje ventanaFichaje = new Fichaje(usuario);
                ventanaFichaje.Show();

                this.Close();
            }
            else
            {
                MostrarCodigoIncorrecto();
                tbCodigo.Focus();
            }
        }
        private void Administracion_Click(object sender, RoutedEventArgs e) {
            
        }


        //VALIDACIÓN
        private void ValidateNumberInput(object sender, TextCompositionEventArgs e)
        {
            Regex regex = new("[^0-9]+");
            e.Handled = regex.IsMatch(e.Text);
        }

        private void MostrarCodigoIncorrecto()
        {
            StringAnimationUsingKeyFrames textAnim = new();
            textAnim.KeyFrames.Add(new DiscreteStringKeyFrame(value: "Código Incorrecto", keyTime: TimeSpan.FromSeconds(0)));
            textAnim.KeyFrames.Add(new DiscreteStringKeyFrame(value: "Entre su código", keyTime: TimeSpan.FromSeconds(1)));

            TextBlock placeholderTextBlock = (TextBlock)tbCodigo.Template.FindName("placeholderTextBlock", tbCodigo);
            placeholderTextBlock.BeginAnimation(TextBlock.TextProperty, textAnim);
        }


        //FOCUS
        private void Focus_tbCodigo(object sender, RoutedEventArgs e)
        {
            tbCodigo.Text = string.Empty;
        }


        //CERRAR APLICACIÓN
        private void CerrarApp_Click(object sender, RoutedEventArgs e)
        {
            // Lógica para cerrar la aplicación
            Application.Current.Shutdown();
        }

        private void Hyperlink_Click(object sender, RoutedEventArgs e)
        {
            Registro registro = new Registro();
            this.Close();
            registro.Show();
            
        }

        public void ObtenerDireccionIP()
        {
            try
            {
                // Obtener todas las interfaces de red
                NetworkInterface[] networkInterfaces = NetworkInterface.GetAllNetworkInterfaces();

                // Priorizar la búsqueda de una interfaz Wi-Fi activa
                NetworkInterface activeInterface = networkInterfaces.FirstOrDefault(
                    iface => iface.NetworkInterfaceType == NetworkInterfaceType.Wireless80211 &&
                             iface.OperationalStatus == OperationalStatus.Up);

                // Si no hay Wi-Fi activa, buscar cualquier interfaz Ethernet u otras interfaces de red activas
                if (activeInterface == null)
                {
                    activeInterface = networkInterfaces.FirstOrDefault(
                        iface => iface.OperationalStatus == OperationalStatus.Up &&
                                 (iface.NetworkInterfaceType == NetworkInterfaceType.Ethernet ||
                                  iface.NetworkInterfaceType == NetworkInterfaceType.GigabitEthernet ||
                                  iface.NetworkInterfaceType == NetworkInterfaceType.FastEthernetT ||
                                  iface.NetworkInterfaceType == NetworkInterfaceType.FastEthernetFx ||
                                  iface.NetworkInterfaceType == NetworkInterfaceType.Unknown)); // Busca cualquier tipo de red activa
                }

                if (activeInterface != null)
                {                    
                    // Obtener las propiedades de configuración de la interfaz activa (Wi-Fi, Ethernet, etc.)
                    IPInterfaceProperties ipProperties = activeInterface.GetIPProperties();

                    // Filtrar las direcciones IPv4
                    UnicastIPAddressInformation ipv4Address = ipProperties.UnicastAddresses
                        .FirstOrDefault(addr => addr.Address.AddressFamily == AddressFamily.InterNetwork);

                    if (ipv4Address != null)
                    {
                        // Mostrar la dirección IPv4 en el TextBlock
                        txtDireccionIP.Text = "Dirección IP: " + ipv4Address.Address.ToString() + " / DNET-RRHH";
                    }
                    else
                    {
                        // Si no se encuentra una dirección IPv4, mostrar un mensaje adecuado
                        txtDireccionIP.Text = "No se encontró una dirección IPv4 válida";
                    }
                }
                else
                {
                    // Si no se encuentra ninguna interfaz de red activa, mostrar un mensaje adecuado
                    txtDireccionIP.Text = "No se encontró una interfaz de red activa";
                }
            }
            catch (Exception ex)
            {
                CL_CHRONO.Logger.LogException("ERROR al obtener la dirección IP: ", ex);
                txtDireccionIP.Text = "Error al obtener la dirección IP";
            }
        }

    }
}
