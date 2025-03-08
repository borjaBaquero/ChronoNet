using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Data.SqlServerCe;

using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;
using CL_CHRONO.Controlador;
using CL_CHRONO.Modelo;

namespace CL_CHRONO.Vista
{
    /// <summary>
    /// Lógica de interacción para Registro.xaml
    /// </summary>
    public partial class Registro : Window
    {
        private DAOTUsuarios _daoUsuarios;
        private TUsuario _tUsuario;
        public Registro()
        {
            InitializeComponent();
        }

        private void Window_Loaded(object sender, RoutedEventArgs e)
        {
            // Obtener el tamaño de la pantalla
            double screenWidth = SystemParameters.PrimaryScreenWidth;
            double screenHeight = SystemParameters.PrimaryScreenHeight;

            // Establecer la posición de la ventana en el centro de la pantalla
            Left = (screenWidth - Width) / 2;
            Top = (screenHeight - Height) / 2;
        }

        private void btnVolver_Click(object sender, RoutedEventArgs e)
        {
            Inicio ini = new Inicio();
            this.Close();
            ini.Show();
        }

        private void btnRegistrarse_Click(object sender, RoutedEventArgs e)
        {
            // Validar que todos los campos están llenos
            if (string.IsNullOrWhiteSpace(txtDni.Text) ||
                string.IsNullOrWhiteSpace(txtCodigo.Text) ||
                string.IsNullOrWhiteSpace(txtNombre.Text) ||
                string.IsNullOrWhiteSpace(txtApellidos.Text))
            {
                MessageBox.Show("Por favor, completa todos los campos.", "Error de validación", MessageBoxButton.OK, MessageBoxImage.Error);
                return;
            }

            // Validar DNI (solo números)
            string dni = txtDni.Text.Trim();
            if (!ValidarDNI(dni))
            {
                MessageBox.Show("El DNI debe tener exactamente 8 números.", "Error de validación", MessageBoxButton.OK, MessageBoxImage.Error);
                return;
            }

            // Validar Código (debe ser un número)
            if (!int.TryParse(txtCodigo.Text, out int codigo))
            {
                MessageBox.Show("El código debe ser un número válido.", "Error de validación", MessageBoxButton.OK, MessageBoxImage.Error);
                return;
            }

            // Validar nombre y apellidos (no vacíos)
            string nombre = txtNombre.Text.Trim();
            string apellidos = txtApellidos.Text.Trim();
            if (string.IsNullOrWhiteSpace(nombre) || string.IsNullOrWhiteSpace(apellidos))
            {
                MessageBox.Show("El nombre y los apellidos no pueden estar vacíos.", "Error de validación", MessageBoxButton.OK, MessageBoxImage.Error);
                return;
            }

            // Si todo es válido, crear el nuevo usuario
            TUsuario nuevoUsuario = new TUsuario()
            {
                DNI = dni,
                Codigo = codigo.ToString(),
                Nombre = nombre,
                Apellidos = apellidos,
                AdminBool = 0
            };

            // Guardar el nuevo usuario en la base de datos
            DAOTUsuarios daoTUsuarios = new DAOTUsuarios();
            bool registroExitoso = daoTUsuarios.RegistrarUsuario(nuevoUsuario);

            if (registroExitoso)
            {
                MessageBox.Show("Registro completado con éxito.", "Registro exitoso", MessageBoxButton.OK, MessageBoxImage.Information);
                Inicio ventanainicio = new Inicio();
                ventanainicio.Show();
                this.Close();
            }
            else
            {
                MessageBox.Show("Hubo un problema al registrar el usuario. Intenta de nuevo.", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        private void CerrarApp_Click(object sender, RoutedEventArgs e)
        {
            // Lógica para cerrar la aplicación
            Application.Current.Shutdown();
        }
        private bool ValidarDNI(string dni)
        {
            // Validar longitud (debe tener exactamente 8 caracteres)
            if (dni.Length != 8)
                return false;

            // Validar que todos los caracteres sean numéricos
            if (!dni.All(char.IsDigit))
                return false;

            // Si pasa las validaciones, el DNI es válido
            return true;
        }

        private void TxtDni_PreviewTextInput(object sender, TextCompositionEventArgs e)
        {
            // Verificar si el carácter ingresado es un dígito (0-9)
            e.Handled = !char.IsDigit(e.Text, 0);
        }

    }
}
