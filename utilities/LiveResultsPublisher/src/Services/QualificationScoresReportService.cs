using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using LiveResultsPublisher.Objects;
using LiveResultsPublisher.ReportObjects.QualificationScores;
using LiveResultsPublisher.Repos;

namespace LiveResultsPublisher.Services
{
    public class QualificationScoresReportService
    {
        private readonly QualificationScoresRepo m_scoresRepo;
        private readonly TournamentRepo m_tournamentRepo;

        public QualificationScoresReportService(
            QualificationScoresRepo _scoresRepo,
            TournamentRepo _tournamentRepo)
        {
            m_scoresRepo = _scoresRepo;
            m_tournamentRepo = _tournamentRepo;
        }

        public QualificationScoresReport GetReportData(string _competitionCode)
        {
            Tournament t = m_tournamentRepo.GetTournament(_competitionCode);

            if (t != null)
            {
                List<ParticipantDivision> divisions = m_scoresRepo.GetResults(_competitionCode);

                QualificationScoresReport report = new QualificationScoresReport
                {
                    Title = t.Name,
                    Divisions = divisions,
                    LastUpdated = DateTime.Now.ToString("g")
                };

                if (t.Location != null)
                {
                    report.Subtitle = $"At {t.Location}";
                }

                if (t.StartDate != null)
                {
                    if (t.EndDate != null
                        && !t.StartDate.Equals(t.EndDate))
                    {
                        report.DateRange = $"From {t.StartDate.Value.ToString("d")} to {t.EndDate.Value.ToString("d")}";
                    }
                    else
                    {
                        report.DateRange = $"On {t.StartDate.Value.ToString("d")}";
                    }
                }

                return report;
            }

            return null;
        }

    }
}
