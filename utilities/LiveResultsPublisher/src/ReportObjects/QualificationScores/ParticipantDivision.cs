using System.Collections.Generic;

namespace LiveResultsPublisher.ReportObjects.QualificationScores
{
    public class ParticipantDivision
    {
        public ParticipantDivision()
        {
            ParticipantClasses = new List<ParticipantClass>();
        }

        public string DivisionName { get; set; }

        public string DivisionCode { get; set; }

        public List<ParticipantClass> ParticipantClasses { get; set; }

    }
}
