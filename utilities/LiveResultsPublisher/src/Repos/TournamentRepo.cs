using System.Collections.Generic;
using System.Data;
using LiveResultsPublisher.Helpers;
using LiveResultsPublisher.Objects;

namespace LiveResultsPublisher.Repos
{
    public class TournamentRepo
    {
        private Database m_db;

        public TournamentRepo(Database _db)
        {
            m_db = _db;
        }

        public List<Tournament> GetTournaments()
        {
            string SQL = @"
                SELECT 	ToId		AS TournamentId,
		                ToCode		AS TournamentCode,
                        ToName		AS Name,
                        ToComDescr	AS Host,
                        ToWhere		As Location,
                        ToWhenFrom	AS StartDate,
		                ToWhenTo	AS EndDate
                FROM Tournament ";

            List<Tournament> results = new List<Tournament>();

            DataTable table = m_db.GetData(SQL);

            foreach (DataRow row in table.Rows)
            {
                var t = PopulateFromRow(row);
                results.Add(t);
            }

            return results;
        }

        public Tournament GetTournament(string _competitionCode)
        {
            string SQL = @"
                SELECT 	ToId		AS TournamentId,
		                ToCode		AS TournamentCode,
                        ToName		AS Name,
                        ToComDescr	AS Host,
                        ToWhere		As Location,
                        ToWhenFrom	AS StartDate,
		                ToWhenTo	AS EndDate
                FROM Tournament 
                WHERE ToCode = @tournamentCode ";

            DataTable table = m_db.GetData(SQL, "tournamentCode", _competitionCode);

            if (table.Rows.Count > 0)
            {
                return PopulateFromRow(table.Rows[0]);    
            }

            return null;
        }

        private Tournament PopulateFromRow(DataRow row)
        {
            Tournament t = new Tournament
            {
                TournamentId = (int)row.GetUInt("TournamentId"),
                TournamentCode = row.GetString("TournamentCode"),
                Name = row.GetString("Name"),
                Host = row.GetString("Host"),
                Location = row.GetString("Location"),
                StartDate = row.GetNullableDate("StartDate"),
                EndDate = row.GetNullableDate("EndDate")
            };

            return t;
        }
    }
}
