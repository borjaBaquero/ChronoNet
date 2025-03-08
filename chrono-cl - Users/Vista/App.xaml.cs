using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlServerCe;

using System.Data;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;
using System.Windows;
using CL_CHRONO.Datos;

namespace CL_CHRONO.Vista
{
    /// <summary>
    /// Interaction logic for App.xaml
    /// </summary>
    public partial class App : Application
    {
        private static Mutex mutex = null;
        [STAThread]
        public static void Main()
        {
            const string appName = "CL_CHRONO_Instance";
            bool createdNew;

            mutex = new Mutex(true, appName, out createdNew);

            if (!createdNew)
            {
                // Si ya existe una instancia, cierra esta
                MessageBox.Show("La aplicación ya está en ejecución.");
                return;
            }

            crearbasededatos();
            App app = new App();
            app.InitializeComponent();
            app.Run(new Inicio());
        }

        private static void crearbasededatos()
        {
            try
            {
                var dbConnection = new DBConnection();

            }
            catch (Exception e)
            {
                System.Windows.MessageBox.Show("Error al iniciar la base de datos: " + e.Message, "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }
    }
}
