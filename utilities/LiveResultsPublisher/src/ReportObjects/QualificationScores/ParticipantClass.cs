using System.Collections.Generic;

namespace LiveResultsPublisher.ReportObjects.QualificationScores
{
    public class ParticipantClass
    {
        public ParticipantClass()
        {
            Participants = new List<ParticipantResult>();
        }

        public string ClassName { get; set; }

        public string ClassSex { get; set; }

        public string ClassCode { get; set; }

        public List<ParticipantResult> Participants { get; set; }
    }
}
