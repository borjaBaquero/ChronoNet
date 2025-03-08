using System;
using System.Collections.Generic;
using System.Data;
using System.Data.SqlClient;
using System.Globalization;
using System.Linq;
using System.Text;
using CL_CHRONO.Datos;
using System.Data.SqlServerCe;

using System.Threading.Tasks;
using CL_CHRONO.Modelo;
using Microsoft.Data.Sqlite;
using MySql.Data.MySqlClient;
using System.Net.Http;
using System.Windows;
using Org.BouncyCastle.Asn1.Ocsp;

namespace CL_CHRONO.Controlador
{
    public class DAOTRegistros
    {
        // Instancia única de HttpClient
        private static readonly HttpClient client = new HttpClient(new HttpClientHandler
        {
            ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => true
        })
        {
            Timeout = TimeSpan.FromSeconds(5) // Definir el timeout solo una vez
        };
        public List<TRegistro> ObtenerTodosLosRegistros()
        {
            List<TRegistro> results = new();

            // Consulta explícita en lugar de SELECT *
            string sqlQuery = @"SELECT idRegistro, Tipo, FechaHora, Incidencia, Recogido, idUsuario, Token FROM TRegistro";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    using (var dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            TRegistro registro = new()
                            {
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                Tipo = dataReader.GetInt16(dataReader.GetOrdinal("Tipo")),
                                FechaHora = dataReader.GetDateTime(dataReader.GetOrdinal("FechaHora")),
                                Incidencia = dataReader.GetString(dataReader.GetOrdinal("Incidencia")),
                                Recogido = dataReader.GetInt16(dataReader.GetOrdinal("Recogido")),
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                Token = dataReader.IsDBNull(dataReader.GetOrdinal("Token")) ? null : dataReader.GetString(dataReader.GetOrdinal("Token"))
                            };

                            results.Add(registro);
                        }
                    }
                }
            }

            return results;
        }

        public List<TRegistro> ObtenerRegistrosNoSincronizados()
        {
            List<TRegistro> results = new();

            // Consulta explícita de columnas
            string sqlQuery = @"SELECT idRegistro, Tipo, FechaHora, Incidencia, Recogido, idUsuario, Token 
                        FROM TRegistro 
                        WHERE Recogido = 0";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    using (var dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            TRegistro registro = new()
                            {
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                Tipo = (short)dataReader.GetInt32(dataReader.GetOrdinal("Tipo")), // SQLite usa INTEGER, se puede manejar como Int32
                                FechaHora = DateTime.Parse(dataReader.GetString(dataReader.GetOrdinal("FechaHora"))), // SQLite guarda fechas como texto
                                Incidencia = dataReader.GetString(dataReader.GetOrdinal("Incidencia")),
                                Recogido = (short)dataReader.GetInt32(dataReader.GetOrdinal("Recogido")), // SQLite usa INTEGER, se puede manejar como Int32
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                Token = dataReader.IsDBNull(dataReader.GetOrdinal("Token")) ? null : dataReader.GetString(dataReader.GetOrdinal("Token"))
                            };

                            results.Add(registro);
                        }
                    }
                }
            }

            return results;
        }

        public bool InsertarRegistro(TRegistro registro)
        {
            bool result = false;

            // Obtener el último registro del usuario para el día actual
            TRegistro? ultimoRegistro = ObtenerUltimoRegistroDelDia(registro.idUsuario);

            string codEmpresa = ObtenerCodEmpresaPrimeraEmpresa();

            // Validar el tipo de registro y asignar el token en consecuencia
            if (registro.Tipo == 0) // Entrada
            {
                if (ultimoRegistro != null && ultimoRegistro.Tipo == 0)
                {
                    // Si el último registro ya es de entrada, no se permite otra entrada
                    throw new InvalidOperationException("No se puede registrar otra entrada. El último registro es ya una entrada.");
                }

                // Genera un nuevo token para la entrada
                //HABRIA QUE CAMBIARLO POR EL COD EMPRESA
                registro.Token = codEmpresa + Guid.NewGuid().ToString();
            }
            else if (registro.Tipo == 1) // Salida
            {
                if (ultimoRegistro == null || ultimoRegistro.Tipo != 0 && ultimoRegistro.Tipo != 3)
                {
                    // No se permite registrar una salida sin una entrada o reanudación previa
                    throw new InvalidOperationException("No se puede registrar una salida sin una entrada o reanudación previa.");
                }

                // Reutilizar el token del último registro de entrada o reanudación
                registro.Token = ultimoRegistro.Token;
            }
            else if (registro.Tipo == 2) // Pausa
            {
                if (ultimoRegistro == null || ultimoRegistro.Tipo != 0 && ultimoRegistro.Tipo != 3)
                {
                    // No se permite registrar una pausa sin una entrada o reanudación previa
                    throw new InvalidOperationException("No se puede registrar una pausa sin una entrada o reanudación previa.");
                }

                // Reutilizar el token del último registro de entrada o reanudación
                registro.Token = ultimoRegistro.Token;
            }
            else if (registro.Tipo == 3) // Reanudar
            {
                if (ultimoRegistro == null || ultimoRegistro.Tipo != 2)
                {
                    // No se permite reanudar sin una pausa previa
                    throw new InvalidOperationException("No se puede reanudar sin una pausa previa.");
                }

                // Reutilizar el token del último registro de pausa
                registro.Token = ultimoRegistro.Token;
            }

            // Inserción en la base de datos con el token determinado
            string sqlQueryInsert = @"INSERT INTO TRegistro (Tipo, FechaHora, Incidencia, Recogido, idUsuario, Token)
                              VALUES (@Tipo, @FechaHora, @Incidencia, @Recogido, @idUsuario, @Token)";

            using (DBConnection conn = new DBConnection())
            {
                SqliteCommand cmdInsert = new SqliteCommand(sqlQueryInsert, conn.GetConnection());

                cmdInsert.Parameters.AddWithValue("@Tipo", registro.Tipo);
                cmdInsert.Parameters.AddWithValue("@FechaHora", registro.FechaHora);
                cmdInsert.Parameters.AddWithValue("@Incidencia", registro.Incidencia);
                cmdInsert.Parameters.AddWithValue("@Recogido", registro.Recogido);
                cmdInsert.Parameters.AddWithValue("@idUsuario", registro.idUsuario);
                cmdInsert.Parameters.AddWithValue("@Token", registro.Token);

                int rowsAffected = cmdInsert.ExecuteNonQuery();
                result = rowsAffected > 0;
            }
            // Después de insertar el registro, calculamos las horas trabajadas si es un registro de "Salida"
            if (registro.Tipo == 1)
            {
                DAOTControlHoras dAOTControlHoras = new DAOTControlHoras();
                dAOTControlHoras.insertarControl(registro.Token);
            }
            return result;
        }

        private string ObtenerCodEmpresaPrimeraEmpresa()
        {
            string codEmpresa = "DEFAULT"; // Valor predeterminado
            string sqlQuery = "SELECT CodEmpresa FROM TEmpresa ORDER BY idEmpresa LIMIT 1";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    // Ejecutar la consulta y manejar el resultado
                    object result = cmd.ExecuteScalar();
                    codEmpresa = result as string ?? "DEFAULT"; // Si es null, retorna "DEFAULT"
                }
            }

            return codEmpresa;
        }


        public TRegistro? BuscarRegistroPorUsuarioFechaHoy(int idUsuario, int tipo)
        {
            TRegistro? registro = null;
            DateTime hoy = DateTime.Now;

            // Consulta con sintaxis compatible con SQLite
            string sqlQuery = @"SELECT idRegistro, Tipo, FechaHora, Incidencia, idUsuario, Token 
                        FROM TRegistro 
                        WHERE DATE(FechaHora) = @Hoy 
                          AND idUsuario = @idUsuario 
                          AND Tipo = @Tipo";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    // Configurar parámetros
                    cmd.Parameters.AddWithValue("@Hoy", hoy.ToString("yyyy-MM-dd", CultureInfo.InvariantCulture));
                    cmd.Parameters.AddWithValue("@idUsuario", idUsuario);
                    cmd.Parameters.AddWithValue("@Tipo", tipo);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        if (dataReader.Read())
                        {
                            registro = new TRegistro
                            {
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                Tipo = dataReader.GetInt16(dataReader.GetOrdinal("Tipo")),
                                FechaHora = dataReader.GetDateTime(dataReader.GetOrdinal("FechaHora")),
                                Incidencia = dataReader.GetString(dataReader.GetOrdinal("Incidencia")),
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                Token = dataReader.IsDBNull(dataReader.GetOrdinal("Token")) ? null : dataReader.GetString(dataReader.GetOrdinal("Token"))
                            };
                        }
                    }
                }
            }

            return registro;
        }


        public TRegistro? ObtenerUltimoRegistroDelDia(int idUsuario)
        {
            TRegistro? registro = null;

            DateTime hoy = DateTime.Today;

            // Consulta con sintaxis compatible con SQLite
            string sqlQuery = @"
        SELECT idRegistro, Tipo, FechaHora, Incidencia, idUsuario, Token
        FROM TRegistro
        WHERE idUsuario = @idUsuario 
          AND DATE(FechaHora) = @Hoy
        ORDER BY FechaHora DESC
        LIMIT 1";

            using (DBConnection conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@idUsuario", idUsuario);
                    cmd.Parameters.AddWithValue("@Hoy", hoy.ToString("yyyy-MM-dd", CultureInfo.InvariantCulture));

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        if (dataReader.Read())
                        {
                            registro = new TRegistro
                            {
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                Tipo = dataReader.GetInt16(dataReader.GetOrdinal("Tipo")),
                                FechaHora = dataReader.GetDateTime(dataReader.GetOrdinal("FechaHora")),
                                Incidencia = dataReader.GetString(dataReader.GetOrdinal("Incidencia")),
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                Token = dataReader.IsDBNull(dataReader.GetOrdinal("Token")) ? null : dataReader.GetString(dataReader.GetOrdinal("Token"))
                            };
                        }
                    }
                }
            }

            return registro;
        }



        public List<TRegistro> ObtenerListaRegistros()
        {
            List<TRegistro> results = new();

            // Consulta explícita de columnas
            string sqlQuery = @"SELECT idRegistro, Tipo, FechaHora, Incidencia, Recogido, idUsuario, Token FROM TRegistro";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    using (var dataReader = cmd.ExecuteReader())
                    {
                        // Obtener los índices de las columnas antes del bucle
                        int idRegistroIndex = dataReader.GetOrdinal("idRegistro");
                        int tipoIndex = dataReader.GetOrdinal("Tipo");
                        int fechaHoraIndex = dataReader.GetOrdinal("FechaHora");
                        int incidenciaIndex = dataReader.GetOrdinal("Incidencia");
                        int recogidoIndex = dataReader.GetOrdinal("Recogido");
                        int idUsuarioIndex = dataReader.GetOrdinal("idUsuario");
                        int tokenIndex = dataReader.GetOrdinal("Token");

                        while (dataReader.Read())
                        {
                            TRegistro registro = new()
                            {
                                idRegistro = dataReader.GetInt32(idRegistroIndex),
                                Tipo = dataReader.GetInt16(tipoIndex),
                                FechaHora = dataReader.GetDateTime(fechaHoraIndex),
                                Incidencia = dataReader.GetString(incidenciaIndex),
                                Recogido = dataReader.GetInt16(recogidoIndex),
                                idUsuario = dataReader.GetInt32(idUsuarioIndex),
                                Token = dataReader.IsDBNull(tokenIndex) ? null : dataReader.GetString(tokenIndex)
                            };

                            results.Add(registro);
                        }
                    }
                }
            }

            return results;
        }


        public TRegistro? ObtenerEntradaCorrespondiente(int idUsuario, DateTime fechaSalida)
        {
            TRegistro? entradaCorrespondiente = null;

            // Consulta con LIMIT 1 para compatibilidad con SQLite
            string sqlQuery = @"SELECT idRegistro, Tipo, FechaHora, Incidencia, idUsuario, Token
                        FROM TRegistro
                        WHERE idUsuario = @idUsuario AND Tipo = 0 AND FechaHora < @FechaSalida
                        ORDER BY FechaHora DESC
                        LIMIT 1";

            using (DBConnection conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    // Asignar valores a los parámetros
                    cmd.Parameters.AddWithValue("@idUsuario", idUsuario);
                    cmd.Parameters.AddWithValue("@FechaSalida", fechaSalida);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        if (dataReader.Read())
                        {
                            // Obtener índices una vez antes de asignar
                            int idRegistroIndex = dataReader.GetOrdinal("idRegistro");
                            int tipoIndex = dataReader.GetOrdinal("Tipo");
                            int fechaHoraIndex = dataReader.GetOrdinal("FechaHora");
                            int incidenciaIndex = dataReader.GetOrdinal("Incidencia");
                            int idUsuarioIndex = dataReader.GetOrdinal("idUsuario");
                            int tokenIndex = dataReader.GetOrdinal("Token");

                            entradaCorrespondiente = new TRegistro
                            {
                                idRegistro = dataReader.GetInt32(idRegistroIndex),
                                Tipo = dataReader.GetInt16(tipoIndex),
                                FechaHora = dataReader.GetDateTime(fechaHoraIndex),
                                Incidencia = dataReader.GetString(incidenciaIndex),
                                idUsuario = dataReader.GetInt32(idUsuarioIndex),
                                Token = dataReader.IsDBNull(tokenIndex) ? null : dataReader.GetString(tokenIndex)
                            };
                        }
                    }
                }
            }

            return entradaCorrespondiente;
        }


        public void MarcarRegistrosComoRecogidos(List<int>idsFichajes)
        {
            if (idsFichajes == null || idsFichajes.Count == 0)
                return; // No hay nada que actualizar
            //dara un error de escalabilidad si la consulta supera 999 elementos, si sucede esto habra que dividirlos en lotes
            string sqlQuery = $"UPDATE TRegistro SET Recogido = 1 WHERE idRegistro IN ({string.Join(",", idsFichajes.Select((_, i) => $"@id{i}"))})";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    // Agregar los parámetros dinámicamente
                    for (int i = 0; i < idsFichajes.Count; i++)
                    {
                        cmd.Parameters.AddWithValue($"@id{i}", idsFichajes[i]);
                    }

                    // Ejecutar la consulta
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public void ActualizarRegistro(TRegistro registro)
        {
            string sqlQuery = @"UPDATE TRegistro SET Recogido = @Recogido WHERE idRegistro =  @idRegistro";

            using (DBConnection conn = new())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@Tipo", registro.Tipo);
                    cmd.Parameters.AddWithValue("@FechaHora", registro.FechaHora);
                    cmd.Parameters.AddWithValue("@Incidencia", registro.Incidencia);
                    cmd.Parameters.AddWithValue("@Recogido", registro.Recogido);
                    cmd.Parameters.AddWithValue("@idUsuario", registro.idUsuario);
                    cmd.Parameters.AddWithValue("@Token", registro.Token);
                    cmd.Parameters.AddWithValue("@idRegistro", registro.idRegistro);

                    cmd.ExecuteNonQuery();
                }
            }
        }
        public List<TRegistro> ObtenerRegistrosMes(TUsuario usuario, string mes, int tipo)
        {
            List<TRegistro> results = new List<TRegistro>();

            // Parsear el mes y manejar errores
            if (!DateTime.TryParseExact(mes, "MMMM", CultureInfo.CurrentCulture, DateTimeStyles.None, out DateTime mesFecha))
            {
                throw new ArgumentException("El mes proporcionado no es válido.");
            }

            string sqlQuery = @"
        SELECT idRegistro, Tipo, FechaHora, Incidencia, idUsuario 
        FROM TRegistro
        WHERE idUsuario = @idUsuario 
          AND strftime('%m', FechaHora) = @Mes 
          AND strftime('%Y', FechaHora) = @Anio 
          AND Tipo = @Tipo";

            using (DBConnection conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    // Asignar parámetros
                    cmd.Parameters.AddWithValue("@idUsuario", usuario.idUsuario);
                    cmd.Parameters.AddWithValue("@Mes", mesFecha.Month.ToString("D2")); // Mes con dos dígitos
                    cmd.Parameters.AddWithValue("@Anio", DateTime.Now.Year.ToString());
                    cmd.Parameters.AddWithValue("@Tipo", tipo);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        while (dataReader.Read())
                        {
                            // Mapeo del lector a la entidad
                            TRegistro registro = new TRegistro
                            {
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                Tipo = dataReader.GetInt16(dataReader.GetOrdinal("Tipo")),
                                FechaHora = dataReader.GetDateTime(dataReader.GetOrdinal("FechaHora")),
                                Incidencia = dataReader.IsDBNull(dataReader.GetOrdinal("Incidencia")) ? string.Empty : dataReader.GetString(dataReader.GetOrdinal("Incidencia")),
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario"))
                            };

                            results.Add(registro);
                        }
                    }
                }
            }

            return results;
        }

        private int ObtenerNumeroMes(string nombreMes)
        {
            // Utilizamos CultureInfo.CurrentCulture para obtener el formato de fecha local
            DateTimeFormatInfo formatoFecha = CultureInfo.CurrentCulture.DateTimeFormat;
            return formatoFecha.MonthNames.ToList().IndexOf(nombreMes) + 1;
        }

        public TRegistro? ObtenerUltimoReanudar(int idUsuario)
        {
            TRegistro? ultimoReanudar = null;

            // Consulta compatible con SQLite
            string sqlQuery = @"
        SELECT idRegistro, Tipo, FechaHora, Incidencia, idUsuario, Token
        FROM TRegistro
        WHERE idUsuario = @idUsuario AND Tipo = 3
        ORDER BY FechaHora DESC
        LIMIT 1";

            using (DBConnection conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@idUsuario", idUsuario);

                    using (var dataReader = cmd.ExecuteReader())
                    {
                        if (dataReader.Read())
                        {
                            // Mapeo del lector a la entidad
                            ultimoReanudar = new TRegistro
                            {
                                idRegistro = dataReader.GetInt32(dataReader.GetOrdinal("idRegistro")),
                                Tipo = dataReader.GetInt16(dataReader.GetOrdinal("Tipo")),
                                FechaHora = dataReader.GetDateTime(dataReader.GetOrdinal("FechaHora")),
                                Incidencia = dataReader.GetString(dataReader.GetOrdinal("Incidencia")),
                                idUsuario = dataReader.GetInt32(dataReader.GetOrdinal("idUsuario")),
                                Token = dataReader.IsDBNull(dataReader.GetOrdinal("Token")) ? null : dataReader.GetString(dataReader.GetOrdinal("Token"))
                            };
                        }
                    }
                }
            }

            return ultimoReanudar;
        }


        public List<RegistroDto> ObtenerRegistrosConUsuarios()
        {
            var registrosConUsuarios = new List<RegistroDto>();

            string query = @"
        SELECT TRegistro.idRegistro, TRegistro.Tipo, TRegistro.FechaHora, TRegistro.Incidencia, 
               TRegistro.Recogido, TRegistro.Token, TUsuario.Dni, TUsuario.Nombre, 
               TUsuario.Apellidos, TUsuario.Codigo 
        FROM TRegistro 
        INNER JOIN TUsuario ON TRegistro.idUsuario = TUsuario.idUsuario 
        WHERE TRegistro.Recogido = 0";

            using (var conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(query, conn.GetConnection()))
                {
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            var registro = new RegistroDto
                            {
                                IdRegistro = reader.GetInt32(reader.GetOrdinal("idRegistro")),
                                Tipo = reader.GetInt16(reader.GetOrdinal("Tipo")),
                                FechaHora = reader.GetDateTime(reader.GetOrdinal("FechaHora")),
                                Incidencia = reader.GetString(reader.GetOrdinal("Incidencia")),
                                Recogido = reader.GetInt16(reader.GetOrdinal("Recogido")),
                                Token = reader.IsDBNull(reader.GetOrdinal("Token")) ? null : reader.GetString(reader.GetOrdinal("Token")),
                                Dni = reader.GetString(reader.GetOrdinal("Dni")),
                                Nombre = reader.GetString(reader.GetOrdinal("Nombre")),
                                Apellidos = reader.GetString(reader.GetOrdinal("Apellidos")),
                                Codigo = reader.GetString(reader.GetOrdinal("Codigo"))
                            };

                            registrosConUsuarios.Add(registro);
                        }
                    }
                }
            }

            return registrosConUsuarios;
        }

        public List<RegistroServ> ObtenerRegistrosParaServidor()
        {
            var registrosParaServidor = new List<RegistroServ>();

            string query = @"
            SELECT TRegistro.idRegistro, TRegistro.Tipo, TRegistro.FechaHora, TRegistro.Incidencia, 
                   TRegistro.Token, TUsuario.idUsuario, TUsuario.Dni, TUsuario.Nombre, 
                   TUsuario.Apellidos, TUsuario.Codigo
            FROM TRegistro 
            INNER JOIN TUsuario ON TRegistro.idUsuario = TUsuario.idUsuario 
            WHERE TRegistro.Recogido = 0";

            using (var conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(query, conn.GetConnection()))
                {
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            var registro = new RegistroServ
                            {
                                IdRegistro = reader.GetInt32(reader.GetOrdinal("idRegistro")),
                                Tipo = reader.GetInt16(reader.GetOrdinal("Tipo")),
                                FechaHora = reader.GetDateTime(reader.GetOrdinal("FechaHora")),
                                Incidencia = reader.GetString(reader.GetOrdinal("Incidencia")),
                                Token = reader.IsDBNull(reader.GetOrdinal("Token")) ? null : reader.GetString(reader.GetOrdinal("Token")),
                                Dni = reader.GetString(reader.GetOrdinal("Dni")),
                                Nombre = reader.GetString(reader.GetOrdinal("Nombre")),
                                Apellidos = reader.GetString(reader.GetOrdinal("Apellidos")),
                                Codigo = reader.GetString(reader.GetOrdinal("Codigo"))
                            };

                            registrosParaServidor.Add(registro);
                        }
                    }
                }
            }

            return registrosParaServidor;
        }

        public async Task SincronizarRegistrosPendientes()
        {
            try
            {
                List<RegistroServ> registrosPendientes = ObtenerRegistrosParaServidor();
                if (registrosPendientes == null || !registrosPendientes.Any()) return;

                foreach (var registro in registrosPendientes)
                {
                    int idUsuarioServidor = await VerificarOCrearUsuarioEnServidor(registro);
                    registro.IdUsuario = idUsuarioServidor;
                    if (registro.Tipo == 1) registro.TotalHoras = ObtenerTotalHoras(registro.Token);
                }

                bool sincronizacionExitosa = await EnviarRegistrosAlServidor(registrosPendientes);
                if (sincronizacionExitosa)
                {
                    List<int> idsSincronizados = registrosPendientes.Select(r => r.IdRegistro).ToList();
                    MarcarRegistrosComoRecogidos(idsSincronizados);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Error al sincronizar: {ex.Message}", "Error", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        public async Task<int> VerificarOCrearUsuarioEnServidor(RegistroServ registro)
        {
            try
            {
                string url = "https://chrononet.deltanetsi.es/api/usuarios/verificar";
                using (var localHandler = new HttpClientHandler
                {
                    ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => true
                })
                using (var localClient = new HttpClient(localHandler))
                {
                    var jsonData = Newtonsoft.Json.JsonConvert.SerializeObject(new
                    {
                        Dni = registro.Dni,
                        Nombre = registro.Nombre,
                        Apellidos = registro.Apellidos,
                        Codigo = registro.Codigo,
                        Token = registro.Token,
                        AdminBool = registro.AdminBool
                    });

                    var content = new StringContent(jsonData, Encoding.UTF8, "application/json");
                    var response = await localClient.PostAsync(url, content);

                    if (response.IsSuccessStatusCode)
                    {
                        string responseBody = await response.Content.ReadAsStringAsync();
                        var jsonResponse = Newtonsoft.Json.JsonConvert.DeserializeObject<VerificarUsuarioResponse>(responseBody);

                        if (jsonResponse != null && jsonResponse.IdUsuario.HasValue)
                        {
                            return jsonResponse.IdUsuario.Value;
                        }
                        else
                        {
                            throw new Exception("La respuesta del servidor no contiene un IdUsuario válido.");
                        }
                    }
                    else
                    {
                        throw new Exception($"Error al verificar/crear usuario en el servidor: {response.StatusCode}");
                    }
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Error en VerificarOCrearUsuarioEnServidor: {ex.Message}");
                throw;
            }
        }

        public async Task<bool> EnviarRegistrosAlServidor(List<RegistroServ> registrosPendientes)
        {
            try
            {
                string url = "https://chrononet.deltanetsi.es/api/registros";
                using (var localHandler = new HttpClientHandler
                {
                    ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => true
                })
                using (var localClient = new HttpClient(localHandler))
                {
                    var jsonData = Newtonsoft.Json.JsonConvert.SerializeObject(registrosPendientes);
                    var content = new StringContent(jsonData, Encoding.UTF8, "application/json");

                    var response = await localClient.PostAsync(url, content);
                    return response.IsSuccessStatusCode;
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Error en EnviarRegistrosAlServidor: {ex.Message}");
                return false;
            }
        }

        private decimal? ObtenerTotalHoras(string token)
        {
            string sqlQuery = "SELECT TotalHoras from  TControlHoras WHERE Token =   @Token";
            using(DBConnection conn = new DBConnection())
            {
                using (var cmd = new SqliteCommand(sqlQuery, conn.GetConnection()))
                {
                    cmd.Parameters.AddWithValue("@Token", token);
                    var result = cmd.ExecuteScalar();
                    if(result != null  &&  decimal.TryParse(result.ToString(), out var totalHoras))
                    {
                        return totalHoras;
                    }
                }
            }
            return null;
        }
    }
    public class VerificarUsuarioResponse
    {
        public int? IdUsuario { get; set; }
    }
}

