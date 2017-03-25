using System.Collections.Generic;

namespace LiveResultsPublisher.ReportObjects.QualificationScores
{
    public class QualificationScoresReport
    {
        public QualificationScoresReport()
        {
            Divisions = new List<ParticipantDivision>();
        }

        public string Title { get; set; }

        public string Subtitle { get; set; }

        public string DateRange { get; set; }

        public string LastUpdated { get; set; }

        public List<ParticipantDivision> Divisions { get; set; }

    }
}
