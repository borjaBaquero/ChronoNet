using System;
using System.Data;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Globalization;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using CL_CHRONO.Datos;
using CL_CHRONO.Modelo;
using System.Data.SqlServerCe;
using Microsoft.Data.Sqlite;

namespace CL_CHRONO.Controlador
{
    internal class DAOTTurnos
    {
        public TTurno? BuscarTurnoPorUsuario(int idUsuario)
        {
            TTurno? turno = null;

            string sqlQuery = $@"SELECT * FROM TTurnos WHERE IdUsuario=@IdUsuario";

            using (DBConnection conn = new())
            {
                SqliteCommand cmd = new(sqlQuery, conn.GetConnection());
                cmd.Parameters.AddWithValue("@IdUsuario", idUsuario);

                SqliteDataReader dataReader = cmd.ExecuteReader();

                if (dataReader.Read())
                {
                    turno = new TTurno()
                    {
                        IdTurno = dataReader.GetInt32("IdTurno"),
                        IdUsuario = dataReader.GetInt32("IdUsuario"),
                        IdJornada = dataReader.GetInt32("IdJornada"),
                        IdTipoTurno = dataReader.GetInt32("IdTipoTurno"),
                        Descripcion = dataReader.GetString("Descripcion"),
                        HoraInicio = dataReader.GetDateTime("HoraInicio"),
                        HoraFin = dataReader.GetDateTime("HoraFin"),
                        Observaciones = dataReader.GetString("Observaciones")
                    };
                }
            }

            return turno;
        }
    }
}
