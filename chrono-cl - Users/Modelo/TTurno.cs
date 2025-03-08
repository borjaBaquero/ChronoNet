using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SqlServerCe;


namespace CL_CHRONO.Modelo
{
    public class TTurno
    {
        public int IdTurno { get; set; }
        public int IdUsuario { get; set; }
        public int IdJornada { get; set; }
        public int IdTipoTurno { get; set; }
        public string? Descripcion { get; set; }
        public DateTime HoraInicio { get; set; }
        public DateTime HoraFin { get; set; }
        public string? Observaciones { get; set; }
    }
}
