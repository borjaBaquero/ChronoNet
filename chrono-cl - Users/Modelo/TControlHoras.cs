using System;
using System.ComponentModel.DataAnnotations;
using System.Data.SqlServerCe;


namespace CL_CHRONO.Modelo
{
    public class TControlHoras
    {
        [Key]
        public int IdControlHoras { get; set; }
        public decimal TotalHoras { get; set; }
        public DateTime Fecha { get; set; }
        public string Token { get; set; }

        // Constructor vacío
        public TControlHoras() { }

        // Constructor con parámetros
        public TControlHoras(int idControlHoras, decimal totalHoras, DateTime fecha, string token)
        {
            IdControlHoras = idControlHoras;
            TotalHoras = totalHoras;
            Fecha = fecha;
            Token = token;
        }

        // Método opcional para obtener una representación en string del objeto
        public override string ToString()
        {
            return $"Id: {IdControlHoras}, Horas: {TotalHoras}, Fecha: {Fecha.ToShortDateString()}, Token: {Token}";
        }
    }
}