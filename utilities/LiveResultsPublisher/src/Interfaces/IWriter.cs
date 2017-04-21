namespace LiveResultsPublisher.Interfaces
{
    public interface IWriterService
    {
        void PublishFile(string filename, string body);
    }
}
