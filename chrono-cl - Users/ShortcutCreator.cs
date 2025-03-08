using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Reflection;
using System.Text;
using System.Data.SqlServerCe;

using System.Threading.Tasks;
using IWshRuntimeLibrary;

namespace CL_CHRONO
{
    public class ShortcutCreator
    {
        public void CrearAccesoDirecto()
        {
            //// Ruta del archivo .exe de la aplicación (dentro de la carpeta de compilación)
            //string rutaExe = Path.Combine(Path.GetDirectoryName(Assembly.GetExecutingAssembly().Location), "Chrono.exe");

            //// Ruta del acceso directo en el escritorio
            //string rutaAccesoDirecto = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.Desktop), "Chrono.lnk");

            //// Ruta del icono personalizado
            //string rutaIcono = AppDomain.CurrentDomain.BaseDirectory + "Resources/icon.ico"; // Ruta del archivo de icono (.ico)

            //// Crear un objeto WshShell.
            //WshShell shell = new WshShell();

            //// Crear un objeto para el acceso directo.
            //IWshShortcut shortcut = (IWshShortcut)shell.CreateShortcut(rutaAccesoDirecto);

            //// Establecer las propiedades del acceso directo.
            //shortcut.TargetPath = rutaExe; // Ruta del archivo .exe
            //shortcut.IconLocation = rutaIcono; // Ruta del archivo de icono (.ico)
            //shortcut.Description = "Descripción del acceso directo"; // Descripción opcional
            //shortcut.WorkingDirectory = Path.GetDirectoryName(rutaExe); // Directorio de trabajo opcional

            //// Guardar el acceso directo.
            //shortcut.Save();
        }
    }
}