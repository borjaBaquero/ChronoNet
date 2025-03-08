using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Data.SqlServerCe;

using System.Threading.Tasks;

namespace CL_CHRONO
{
    public static class Logger
    {
        private static bool limpiarArchivosAlCerrar = false;
        private static readonly string logFileName = "registros.log";

        public static void LogMessage(string message, string nombreUsuario, string apellidosUsuario)
        {
            // Obtiene la ruta de la carpeta de la aplicación y la carpeta de logs
            string carpetaApp = AppDomain.CurrentDomain.BaseDirectory;
            string carpetaLogs = Path.Combine(carpetaApp, "logs");

            // Crea la carpeta de logs si no existe
            if (!Directory.Exists(carpetaLogs))
            {
                Directory.CreateDirectory(carpetaLogs);
            }

            // Especifica la ruta del archivo de log
            string logFilePath = Path.Combine(carpetaLogs, logFileName);

            // Configura Trace para escribir en el archivo
            using (StreamWriter streamWriter = File.AppendText(logFilePath))
            {
                // Registra el mensaje en el log con el nombre del usuario
                streamWriter.WriteLine($"{DateTime.Now:yyyy-MM-dd HH:mm:ss} - Usuario: {nombreUsuario} {apellidosUsuario} - {message}");
            }
        }

        public static void LogMessage(string message)
        {
            // Obtiene la ruta de la carpeta de la aplicación y la carpeta de logs
            string carpetaApp = AppDomain.CurrentDomain.BaseDirectory;
            string carpetaLogs = Path.Combine(carpetaApp, "logs");

            // Crea la carpeta de logs si no existe
            if (!Directory.Exists(carpetaLogs))
            {
                Directory.CreateDirectory(carpetaLogs);
            }

            // Especifica la ruta del archivo de log
            string logFilePath = Path.Combine(carpetaLogs, logFileName);

            // Configura Trace para escribir en el archivo
            using (StreamWriter streamWriter = File.AppendText(logFilePath))
            {
                // Registra el mensaje en el log
                streamWriter.WriteLine($"{DateTime.Now:yyyy-MM-dd HH:mm:ss} - {message}");
            }
        }

        public static void LogException(string prefix, Exception ex)
        {
            // Obtiene la ruta de la carpeta de la aplicación y la carpeta de logs
            string carpetaApp = AppDomain.CurrentDomain.BaseDirectory;
            string carpetaLogs = Path.Combine(carpetaApp, "logs");

            // Crea la carpeta de logs si no existe
            if (!Directory.Exists(carpetaLogs))
            {
                Directory.CreateDirectory(carpetaLogs);
            }

            // Especifica la ruta del archivo de log
            string logFilePath = Path.Combine(carpetaLogs, logFileName);

            // Configura Trace para escribir en el archivo
            using (StreamWriter streamWriter = File.AppendText(logFilePath))
            {
                // Registra la excepción en el log con el prefijo
                streamWriter.WriteLine($"{DateTime.Now:yyyy-MM-dd HH:mm:ss} - {prefix} Excepción: {ex.Message}");
                streamWriter.WriteLine($"Stack Trace: {ex.StackTrace}");
                streamWriter.WriteLine("---------------------------------------");
            }
        }

        public static void LimpiarArchivosLogs()
        {
            try
            {
                // Obtiene la ruta de la carpeta de la aplicación y la carpeta de logs
                string carpetaApp = AppDomain.CurrentDomain.BaseDirectory;
                string carpetaLogs = Path.Combine(carpetaApp, "logs");

                // Verifica si la carpeta de logs existe
                if (Directory.Exists(carpetaLogs))
                {
                    // Obtiene todos los archivos .log en la carpeta de logs
                    string[] archivosLogs = Directory.GetFiles(carpetaLogs, "*.log");

                    // Itera sobre los archivos y elimina aquellos que no sean "registros.log"
                    foreach (string archivo in archivosLogs)
                    {
                        if (Path.GetFileName(archivo) != logFileName)
                        {
                            File.Delete(archivo);
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                // Maneja cualquier excepción que pueda ocurrir durante la limpieza de archivos
                Console.WriteLine($"Error al limpiar archivos de logs: {ex.Message}");
            }
        }

        public static void ActivarLimpiarArchivosAlCerrar()
        {
            limpiarArchivosAlCerrar = true;
            AppDomain.CurrentDomain.DomainUnload += (sender, e) =>
            {
                if (limpiarArchivosAlCerrar)
                {
                    LimpiarArchivosLogs();
                }
            };
        }
    }
}
