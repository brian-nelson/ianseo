using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace LiveResultsPublisher.Objects
{
    public class Tournament
    {
        public int TournamentId { get; set; }

        public string TournamentCode { get; set; }

        public string Name { get; set; }

        public string Host { get; set; }

        public string Location { get; set; }

        public DateTime? StartDate { get; set; }

        public DateTime? EndDate { get; set; }
    }
}
