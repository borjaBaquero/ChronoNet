using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Data.SqlServerCe;

using System.Text;
using System.Threading.Tasks;

namespace CL_CHRONO.Modelo
{
    public class TRegistro
    {
        [Key]
        public int idRegistro { get; set; }
        public short Tipo { get; set; }   // 0 = Entrada, 1 = Salida, 2 = Pausa
        public DateTime FechaHora { get; set; }
        public string Incidencia { get; set; }
        public short Recogido { get; set; }
        public int idUsuario { get; set; }
        public string Token { get; set; }

    }
}
