using Org.BouncyCastle.Asn1.Mozilla;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CL_CHRONO.Modelo
{
    public class RegistroServ
    {
        public int IdRegistro { get; set; } // Id del registro local (solo para referencia en la sincronización)
        public int IdUsuario { get; set; } // Id del usuario en el servidor
        public short Tipo { get; set; } // Tipo de registro (entrada, salida, etc.)
        public DateTime FechaHora { get; set; } // Fecha y hora del registro
        public string Incidencia { get; set; } // Incidencia asociada al registro
        public string Token { get; set; } // Token único del registro
        public string Dni { get; set; } // DNI del usuario (para verificar o crear usuario en el servidor)
        public string Nombre { get; set; } // Nombre del usuario
        public string Apellidos { get; set; } // Apellidos del usuario
        public string Codigo { get; set; } // Código del usuario
        public int AdminBool { get; set; } = 0;// AdminBool del usuario
        public decimal? TotalHoras { get; set; } // Total de horas para el registro de salida

    }
}
