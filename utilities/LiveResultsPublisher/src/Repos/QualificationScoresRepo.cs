using System;
using System.Collections.Generic;
using System.Data;
using LiveResultsPublisher.Helpers;
using LiveResultsPublisher.Objects;
using LiveResultsPublisher.ReportObjects.QualificationScores;

namespace LiveResultsPublisher.Repos
{
    public class QualificationScoresRepo
    {
        private Database m_db;

        public QualificationScoresRepo(Database _db)
        {
            m_db = _db;
        }

        public List<ParticipantDivision> GetResults(string _competitionCode)
        {
            const string SQL = @"
                SELECT e.EnName 		AS LastName, 
	                e.EnFirstName 		AS FirstName,
	                c.ClDescription 	AS Class,
                    c.ClId              AS ClassCode,
                    c.ClSex             AS ClassSex,
                    d.DivDescription	AS Division,
                    d.DivId             AS DivisionCode,
                    q.QuSession			AS SessionNumber,
                    q.QuTarget			AS BaleNumber,
                    q.QuLetter			AS TargetPosition,    
                    co.CoName			AS TeamName,
                    q.QuD1Score 		AS Round1Score,
                    q.QuD1Hits  		AS Round1Hits,
                    q.QuD1Rank			AS Round1Rank,
                    q.QuD2Score			AS Round2Score,
                    q.QuD2Hits			AS Round2Hits,
                    q.QuD2Rank			AS Round2Rank,
                    q.QuScore			AS TotalScore,
                    q.QuHits			AS TotalHits,
                    Q.QuGold			AS Total10,
                    Q.QuXnine			AS Total9,
                    Q.QuClRank			AS TotalRank
                FROM Entries e
                JOIN qualifications q on e.EnId = q.QuId
                JOIN classes c on e.EnTournament = c.ClTournament AND e.EnClass = c.ClId
                JOIN divisions d on e.EnTournament = d.DivTournament AND e.EnDivision = d.DivId
                JOIN countries co on e.EnTournament = co.CoTournament AND e.EnCountry = co.COId
                JOIN tournament t on e.EnTournament = t.ToId
                WHERE t.ToCode = @compCode
                AND e.EnIndClEvent = 1
                AND e.EnStatus = 0
                AND q.QuClRank != 0
                ORDER BY d.DivViewOrder, c.ClViewOrder, q.QuClRank ";

            var table = m_db.GetData(SQL, "compCode", _competitionCode);

            Dictionary<string, ParticipantDivision> dDict = new Dictionary<string, ParticipantDivision>();
            Dictionary<string, ParticipantClass> cDict = new Dictionary<string, ParticipantClass>();

            List<ParticipantDivision> divisions = new List<ParticipantDivision>();

            foreach (DataRow row in table.Rows)
            {
                string className = row.GetString("Class");
                string classCode = row.GetString("ClassCode").ToLower();
                string divisionName = row.GetString("Division");
                string divisionCode = row.GetString("DivisionCode").ToLower();
                byte classSex = row.GetTinyInt("ClassSex");

                ParticipantDivision pDivision;
                ParticipantClass pClass;

                if (!dDict.ContainsKey(divisionCode))
                {
                    pDivision = new ParticipantDivision
                    {
                        DivisionName = divisionName,
                        DivisionCode = divisionCode
                    };

                    divisions.Add(pDivision);
                    dDict.Add(divisionCode, pDivision);
                }
                else
                {
                    pDivision = dDict[divisionCode];
                }

                string tempName = divisionCode + "|" + classCode;
                if (!cDict.ContainsKey(tempName))
                {
                    pClass = new ParticipantClass
                    {
                        ClassName = className,
                        ClassCode = classCode,
                        ClassSex = (classSex == 0) ? "m" : "f"
                    };

                    cDict.Add(tempName, pClass);
                    pDivision.ParticipantClasses.Add(pClass);
                }
                else
                {
                    pClass = cDict[tempName];
                }

                ParticipantResult p = new ParticipantResult
                {
                    FirstName = row.GetString("FirstName"),
                    LastName = row.GetString("LastName"),
                    Session = Convert.ToInt32(row.GetByte("SessionNumber")),
                    BaleNumber = row.GetInt("BaleNumber"),
                    TargetPosition = row.GetString("TargetPosition"),
                    Team = row.GetString("TeamName"),
                    Round1Score = row.GetShort("Round1Score"),
                    Round1Hits = row.GetShort("Round1Hits"),
                    Round1Rank = row.GetShort("Round1Rank"),
                    Round2Score = row.GetShort("Round2Score"),
                    Round2Hits = row.GetShort("Round2Hits"),
                    Round2Rank = row.GetShort("Round2Rank"),
                    TotalScore = row.GetInt("TotalScore"),
                    TotalHits = row.GetInt("TotalHits"),
                    TotalRank = row.GetShort("TotalRank"),
                    TotalTens = row.GetInt("Total10"),
                    TotalNines = row.GetInt("Total9")
                };

                pClass.Participants.Add(p);
            }

            return divisions;
        }
    }
}
