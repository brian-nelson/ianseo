using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using MySql.Data.MySqlClient;

namespace LiveResultsPublisher.Objects
{
    public class Database
    {
        private Config m_Config;

        public Database(Config _config)
        {
            m_Config = _config;
        }

        public DataTable GetData(string _sql, string _paramName, object _paramValue)
        {
            DatabaseParameter param = new DatabaseParameter
            {
                Name = _paramName,
                Value = _paramValue
            };

            return GetData(_sql, new[] {param});
        }

        public DataTable GetData(string _sql, IEnumerable<DatabaseParameter> _parameters = null )
        {
            using (var connnection = new MySqlConnection(m_Config.ConnectionString))
            {
                connnection.Open();

                using (var command = new MySqlCommand(_sql))
                {
                    command.Connection = connnection;

                    if (_parameters != null)
                    {
                        foreach (var parameter in _parameters)
                        {
                            command.Parameters.AddWithValue(parameter.Name, parameter.Value);
                        }
                    }
                    
                    MySqlDataAdapter adapter = new MySqlDataAdapter(command);

                    DataTable table = new DataTable();
                    adapter.Fill(table);

                    return table;
                }
            }
        }

    }
}
