using System.IO;
using LiveResultsPublisher.Interfaces;
using LiveResultsPublisher.Objects;

namespace LiveResultsPublisher.Services
{
    public class FileWriterService : IWriterService
    {
        private string m_Folder;

        public FileWriterService(Config _config)
        {
            m_Folder = _config.Folder;
        }

        public void PublishFile(string filename, string body)
        {
            string temp = Path.Combine(m_Folder, filename);

            File.WriteAllText(temp, body);
        }
    }
}
