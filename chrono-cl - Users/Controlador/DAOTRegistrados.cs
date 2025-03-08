using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using CL_CHRONO.Datos;
using System.Data.SqlServerCe;

using CL_CHRONO.Modelo;
using Microsoft.Data.Sqlite;
using System.Linq;

namespace CL_CHRONO.Controlador
{
    public class DAOTRegistrados
    {
        public List<TRegistrado> ObtenerLista(TUsuario usuarioActual)
        {
            // Recuperar registros del usuario actual
            List<TRegistrado> registros = ObtenerRegistrosPorUsuario(usuarioActual.idUsuario);

            // Procesar entradas y salidas, y calcular las horas trabajadas
            return ProcesarRegistrosConHoras(registros);
        }

        // Método para recuperar registros desde la base de datos
        private List<TRegistrado> ObtenerRegistrosPorUsuario(int idUsuario)
        {
            var registros = new List<TRegistrado>();

            string sqlQuery = @"
        SELECT tr.idRegistro, tr.idUsuario, tr.Tipo, tr.FechaHora, tr.Incidencia, 
               tu.Nombre, tu.Apellidos 
        FROM tregistro tr 
        LEFT JOIN tusuario tu ON tu.idUsuario = tr.idUsuario 
        WHERE tr.idUsuario = @idUsuario
        ORDER BY FechaHora DESC";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@idUsuario", idUsuario);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            TRegistrado registrado = new()
                            {
                                Nombre = GetSafeString(dataReader, "Nombre"),
                                Apellidos = GetSafeString(dataReader, "Apellidos"),
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                Tipo = ObtenerTipoMostrar(dataReader.GetInt16(dataReader.GetOrdinal("Tipo"))),
                                FechaHora = dataReader.GetDateTime(dataReader.GetOrdinal("FechaHora")),
                                Incidencia = GetSafeString(dataReader, "Incidencia")
                            };

                            registros.Add(registrado);
                        }
                    }
                }
            }

            return registros;
        }


        private string GetSafeString(SqliteDataReader reader, string columnName)
        {
            int ordinal = reader.GetOrdinal(columnName);
            if (!reader.IsDBNull(ordinal))
            {
                return reader.GetString(ordinal);
            }
            return string.Empty;
        }

        // Método para procesar registros y calcular las horas trabajadas
        private List<TRegistrado> ProcesarRegistrosConHoras(List<TRegistrado> registros)
        {
            var entradas = new List<TRegistrado>();
            var salidas = new List<TRegistrado>();
            var resultados = new List<TRegistrado>();

            // Clasificar registros por tipo
            foreach (var registro in registros)
            {
                switch (registro.Tipo)
                {
                    case "Entrada":
                        entradas.Add(registro);
                        break;
                    case "Salida":
                        salidas.Add(registro);
                        break;
                    default:
                        resultados.Add(registro); // Otros tipos se añaden directamente
                        break;
                }
            }

            // Diccionario para emparejar entradas con salidas
            var entradasEmparejadas = new HashSet<TRegistrado>();

            foreach (var salida in salidas)
            {
                var entradaCorrespondiente = entradas
                    .Where(e => e.idUsuario == salida.idUsuario && e.FechaHora < salida.FechaHora && !entradasEmparejadas.Contains(e))
                    .OrderByDescending(e => e.FechaHora)
                    .FirstOrDefault();

                if (entradaCorrespondiente != null)
                {
                    entradasEmparejadas.Add(entradaCorrespondiente); // Marcar como emparejada

                    // Calcular horas trabajadas
                    TimeSpan horasTrabajadas = salida.FechaHora - entradaCorrespondiente.FechaHora;
                    salida.HorasTrabajadas = new TimeSpan(horasTrabajadas.Hours, horasTrabajadas.Minutes, 0);

                    // Añadir entrada y salida al resultado
                    resultados.Add(entradaCorrespondiente);
                    resultados.Add(salida);
                }
                else
                {
                    // Si no hay entrada, añadir solo la salida
                    resultados.Add(salida);
                }
            }

            // Agregar entradas no emparejadas al resultado
            foreach (var entrada in entradas)
            {
                if (!entradasEmparejadas.Contains(entrada))
                {
                    resultados.Add(entrada);
                }
            }

            // Ordenar todos los registros por fecha antes de devolverlos
            return resultados.OrderBy(r => r.FechaHora).ToList();
        }



        private string ObtenerTipoMostrar(int tipo)
        {
            switch (tipo)
            {
                case 0:
                    return "Entrada";
                case 1:
                    return "Salida";
                case 2:
                    return "Pausa";
                case 3:
                    return "Reanudar";
                default:
                    return "Desconocido";
            }
        }

    }

}