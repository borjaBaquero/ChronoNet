using System;
using System.Collections.Generic;
using System.Data;
using System.Data.SqlClient;
using System.Diagnostics;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using CL_CHRONO.Datos;
using CL_CHRONO.Modelo;
using System.Data.SqlServerCe;

using DK.Standard;
using Microsoft.Data.Sqlite;
namespace CL_CHRONO.Controlador
{
    public class DAOTUsuarios
    {
        public string cadena = "Server = localhost; DataBase = SADYFI_USERS; Trusted_Connection = true";
        public List<TUsuario> ObtenerTodosLosUsuarios()
        {
            List<TUsuario> results = new();

            string sqlQuery = $@"SELECT * FROM TUsuario";

            using (DBConnection conn = new())
            {
                using (SqliteCommand cmd = new(sqlQuery, conn.GetConnection()))
                {
                    using (SqliteDataReader dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            TUsuario usuario = CrearTUsuarioDesdeDataReader(dataReader);
                            results.Add(usuario);
                        }
                    }
                }
            }

            return results;
        }

        public List<TUsuario> ObtenerLista(TUsuario _currentUser)
        {
            List<TUsuario> results = new();

            string sqlQuery = @"SELECT idUsuario, DNI, Codigo, Nombre, Apellidos FROM TUsuario WHERE DNI = @DNI";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@DNI", _currentUser.DNI);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            TUsuario usuario = CrearTUsuarioDesdeDataReader(dataReader);
                            results.Add(usuario);
                        }
                    }
                }
            }
                return results;
        }

        private TUsuario CrearTUsuarioDesdeDataReader(SqliteDataReader dataReader)
        {
            return new TUsuario
            {
                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                DNI = dataReader.IsDBNull(dataReader.GetOrdinal("DNI")) ? null : dataReader.GetString(dataReader.GetOrdinal("DNI")),
                Codigo = dataReader.IsDBNull(dataReader.GetOrdinal("Codigo")) ? null : dataReader.GetString(dataReader.GetOrdinal("Codigo")),
                Nombre = dataReader.IsDBNull(dataReader.GetOrdinal("Nombre")) ? null : dataReader.GetString(dataReader.GetOrdinal("Nombre")),
                Apellidos = dataReader.IsDBNull(dataReader.GetOrdinal("Apellidos")) ? null : dataReader.GetString(dataReader.GetOrdinal("Apellidos")),
            };          
        }

        public bool InsertarUsuario(TUsuario usuario)
        {
            // Validación de los datos
            if (usuario == null || string.IsNullOrWhiteSpace(usuario.DNI) || string.IsNullOrWhiteSpace(usuario.Nombre) || string.IsNullOrWhiteSpace(usuario.Apellidos))
            {
                // Registrar el error o manejarlo fuera de este método
                MessageBox.Show("Rellena todos los campos por favor", "Error de datos", MessageBoxButton.OK, MessageBoxImage.Error);
            }

            // Consulta SQL con parámetros para evitar inyección SQL
            string sqlQuery = @"INSERT INTO TUsuario (DNI, Codigo, Nombre, Apellidos, AdminBool) 
                        VALUES (@DNI, @Codigo, @Nombre, @Apellidos, @AdminBool)";

            try
            {
                using (DBConnection conn = new())
                {
                    using (SqliteCommand cmd = new(sqlQuery, conn.GetConnection()))
                    {
                        // Asignar valores a los parámetros
                        cmd.Parameters.AddWithValue("@DNI", usuario.DNI);
                        cmd.Parameters.AddWithValue("@Codigo", usuario.Codigo);
                        cmd.Parameters.AddWithValue("@Nombre", usuario.Nombre);
                        cmd.Parameters.AddWithValue("@Apellidos", usuario.Apellidos);
                        cmd.Parameters.AddWithValue("@AdminBool", 0); // Establecer AdminBool como 0

                        // Ejecutar el comando y verificar si se insertó correctamente
                        int rowsAffected = cmd.ExecuteNonQuery();
                        return rowsAffected > 0;
                    }
                }
            }
            catch (Exception ex)
            {
                // Registrar el error (usa un logger si tienes uno)
                Console.WriteLine($"Error al insertar usuario");
                return false;
            }
        }

        public bool RegistrarUsuario(TUsuario usuario)
        {
            try
            {
                using (DBConnection conn = new())
                {
                    // Verificar si el usuario con el mismo DNI ya existe
                    string queryCheckDNI = "SELECT COUNT(*) FROM TUsuario WHERE DNI = @DNI";
                    using (var checkCmd = new SqliteCommand(queryCheckDNI, conn.GetConnection()))
                    {
                        checkCmd.Parameters.AddWithValue("@DNI", usuario.DNI);
                        int count = Convert.ToInt32(checkCmd.ExecuteScalar());

                        if (count > 0)
                        {
                            // Si ya existe, devolvemos false
                            return false;
                        }
                    }

                    // Llamar al método InsertarUsuario para realizar la inserción
                    return InsertarUsuario(usuario);
                }
            }
            catch (Exception ex)
            {
                // Registrar la excepción
                CL_CHRONO.Logger.LogException("ERROR al registrar el usuario: ", ex);
                return false;
            }
        }

        public List<TUsuario> ObtenerUsuariosPorCodigo(int codigo)
        {
            List<TUsuario> usuarios = new();

            string sqlQuery = "SELECT * FROM TUsuario WHERE Codigo = @Codigo";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@Codigo", codigo);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            TUsuario usuario = new TUsuario
                            {
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                DNI = dataReader.IsDBNull(dataReader.GetOrdinal("DNI")) ? null : dataReader.GetString(dataReader.GetOrdinal("DNI")),
                                Codigo = dataReader.GetString(dataReader.GetOrdinal("Codigo")),
                                Nombre = dataReader.IsDBNull(dataReader.GetOrdinal("Nombre")) ? null : dataReader.GetString(dataReader.GetOrdinal("Nombre")),
                                Apellidos = dataReader.IsDBNull(dataReader.GetOrdinal("Apellidos")) ? null : dataReader.GetString(dataReader.GetOrdinal("Apellidos")),
                            };
                            usuarios.Add(usuario);
                        }
                    }
                }
            }

            return usuarios;
        }

        public bool ExisteCodigoUsuario(int codigo)
        {
            string sqlQuery = "SELECT COUNT(*) FROM TUsuario WHERE Codigo = @Codigo";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@Codigo", codigo);

                    // Usar Convert.ToInt32 para manejar valores nulos o inesperados
                    int count = Convert.ToInt32(cmd.ExecuteScalar());
                    return count > 0;
                }
            }
        } 
    }
}