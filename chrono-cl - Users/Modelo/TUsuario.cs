using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Data.SqlServerCe;

using System.Text;
using System.Threading.Tasks;

namespace CL_CHRONO.Modelo
{
    public class TUsuario
    {
        [Key]
        public int idUsuario { get; set; }
        public string? DNI { get; set; }
        public string Codigo { get; set; }
        public string? Nombre { get; set; }
        public string? Apellidos { get; set; }
        public int AdminBool { get; set; } = 0;
        public string NombreCompleto => $"{Nombre} {Apellidos}";

    }
}