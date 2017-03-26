using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace LiveResultsPublisher.Helpers
{
    public static class RowHelper
    {
        public static string GetString(this DataRow row, string columnName)
        {
            return (string) row[columnName];
        }

        public static int GetInt(this DataRow row, string columnName)
        {
            return (int)row[columnName];
        }

        public static uint GetUInt(this DataRow row, string columnName)
        {
            return (uint)row[columnName];
        }

        public static byte GetByte(this DataRow row, string columnName)
        {
            return (byte) row[columnName];
        }

        public static byte GetTinyInt(this DataRow row, string columnName)
        {
            return Convert.ToByte(row[columnName]);
        }

        public static bool GetBoolean(this DataRow row, string columnName)
        {
            return (bool) row[columnName];
        }

        public static short GetShort(this DataRow row, string columnName)
        {
            return (short) row[columnName];
        }

        public static DateTime GetDate(this DataRow row, string columnName)
        {
            return (DateTime) row[columnName];
        }

        public static DateTime? GetNullableDate(this DataRow row, string columnName)
        {
            return (DateTime?)row[columnName];
        }
    }
}
