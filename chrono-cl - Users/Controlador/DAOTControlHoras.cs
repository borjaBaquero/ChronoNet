using CL_CHRONO.Datos;
using CL_CHRONO.Modelo;
using System.Data.SqlServerCe;
using System;
using System.Collections.Generic;
using System.Data.Common;
using System.Data.SqlClient;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Data.Sqlite;

namespace CL_CHRONO.Controlador
{
    public class DAOTControlHoras
    {
        public string cadena = "Server = localhost; DataBase = SADYFI_USERS; Trusted_Connection = true";
        public TControlHoras insertarControl(string token)
        {
            var controlHoras = new TControlHoras
            {
                Token = token,
                Fecha = DateTime.Today,
                TotalHoras = calcularHorasTrabajadas(token)
            };

            string query = @"
                INSERT INTO TControlHoras (TotalHoras, Fecha, Token)
                VALUES (@TotalHoras, @Fecha, @Token);
                SELECT last_insert_rowid();";

            using (var conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(query, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@TotalHoras", controlHoras.TotalHoras);
                    cmd.Parameters.AddWithValue("@Fecha", controlHoras.Fecha);
                    cmd.Parameters.AddWithValue("@Token", controlHoras.Token);

                    controlHoras.IdControlHoras = Convert.ToInt32(cmd.ExecuteScalar());
                }
            }

            return controlHoras;
        }

        public decimal calcularHorasTrabajadas(string token)
        {
            var registros = ObtenerRegistrosPorToken(token);
            int totalMinutosTrabajados = 0;
            DateTime? entrada = null;
            int minutosPausa = 0;

            foreach (var registro in registros)
            {
                switch (registro.Tipo)
                {
                    case 0: // Entrada
                        entrada = registro.FechaHora;
                        minutosPausa = 0;
                        break;

                    case 2: // Pausa
                        if (entrada.HasValue)
                            minutosPausa = (int)(registro.FechaHora - entrada.Value).TotalMinutes;
                        break;

                    case 3: // Reanudar
                        if (entrada.HasValue)
                            minutosPausa = 0; // Resetear pausa
                        break;

                    case 1: // Salida
                        if (entrada.HasValue)
                        {
                            int minutosTrabajados = (int)(registro.FechaHora - entrada.Value).TotalMinutes - minutosPausa;
                            totalMinutosTrabajados += minutosTrabajados;
                            entrada = null; // Resetear entrada
                        }
                        break;
                }
            }
            // Convertir las horas y minutos totales a decimal
            double horasDecimal = HorasYMinutosADecimal(totalMinutosTrabajados / 60, totalMinutosTrabajados % 60);
            return (decimal)Math.Round(horasDecimal, 2); // Conversión explícita a decimal
        }

        public List<TRegistro> ObtenerRegistrosPorToken(string token)
        {
            var registros = new List<TRegistro>();

            string query = "SELECT * FROM TRegistro WHERE Token = @Token ORDER BY FechaHora";

            using (var conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(query, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@Token", token);

                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            registros.Add(new TRegistro
                            {
                                idRegistro = reader.GetInt32(reader.GetOrdinal("idRegistro")),
                                Tipo = reader.GetInt16(reader.GetOrdinal("Tipo")),
                                FechaHora = reader.GetDateTime(reader.GetOrdinal("FechaHora")),
                                Token = reader.GetString(reader.GetOrdinal("Token"))
                            });
                        }
                    }
                }
            }

            return registros;
        }

        public decimal? ObtenerTotalHorasPorToken(string token)
        {
            string query = "SELECT TotalHoras FROM TControlHoras WHERE Token = @Token";

            using (var conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(query, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@Token", token);
                    object result = cmd.ExecuteScalar();

                    return result != null ? Convert.ToDecimal(result) : (decimal?)null;
                }
            }
        }

        public static double HorasYMinutosADecimal(int horas, int minutos)
        {
            return Math.Round(horas + (minutos / 60.0), 2);
        }
        public static (int, int) DecimalAHorasYMinutos(double decimalHoras)
        {
            int horas = (int)Math.Floor(decimalHoras);
            int minutos = (int)Math.Round((decimalHoras - horas) * 60);
            return (horas, minutos);
        }
    }
}
