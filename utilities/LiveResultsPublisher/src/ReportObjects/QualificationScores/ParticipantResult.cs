namespace LiveResultsPublisher.ReportObjects.QualificationScores
{
    public class ParticipantResult
    {
        public int Session { get; set; }

        public int BaleNumber { get; set; }

        public string TargetPosition { get; set; }

        public string FirstName { get; set; }

        public string LastName { get; set; }

        public string Team { get; set; }

        public int Round1Score { get; set; }

        public int Round1Hits { get; set; }

        public int Round1Rank { get; set; }

        public int Round2Score { get; set; }

        public int Round2Hits { get; set; }

        public int Round2Rank { get; set; }

        public int TotalRank { get; set; }

        public int TotalScore { get; set; }

        public int TotalHits { get; set; }

        public int TotalTens { get; set; }

        public int TotalNines { get; set; }


    }
}
