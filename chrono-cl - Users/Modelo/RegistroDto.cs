using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SqlServerCe;


namespace CL_CHRONO.Modelo
{
    public class RegistroDto
    {
        public int IdRegistro { get; set; }
        public short Tipo { get; set; }
        public DateTime FechaHora { get; set; }
        public string Incidencia { get; set; }
        public short Recogido { get; set; }
        public string Dni { get; set; }
        public string Nombre { get; set; }
        public string Apellidos { get; set; }
        public string Codigo { get; set; }
        public string Token { get; set; }
        public decimal? TotalHoras { get; set; }
    }
}
