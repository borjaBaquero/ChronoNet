using System;
using System.Data.SqlServerCe;

public class TRegistrado
{
    public string Nombre { get; set; }
    public string Apellidos { get; set; }
    public string NombreCompleto => $"{Nombre} {Apellidos}";
    public int idRegistro { get; set; }
    public int idUsuario { get; set; }
    public string Tipo { get; set; }
    public DateTime FechaHora { get; set; }
    public string Incidencia { get; set; }
    public TimeSpan HorasTrabajadas { get; set; }
    public TimeSpan TiempoPausado { get; set; }



    // Propiedad para mostrar las horas trabajadas en formato "00:00"
    public string HorasTrabajadasFormato
    {
        get
        {
            int horas = (int)HorasTrabajadas.TotalHours;
            int minutos = HorasTrabajadas.Minutes;
            return $"{horas:00}:{minutos:00}";
        }
    }


    public TRegistrado()
    {
        // Inicializamos la nueva propiedad en TimeSpan.Zero
        TiempoPausado = TimeSpan.Zero;
    }
}
