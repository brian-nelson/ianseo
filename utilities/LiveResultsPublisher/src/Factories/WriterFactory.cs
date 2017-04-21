using System;
using LiveResultsPublisher.Interfaces;
using LiveResultsPublisher.Objects;
using LiveResultsPublisher.Services;

namespace LiveResultsPublisher.Factories
{
    public static class WriterFactory
    {
        public static IWriterService GetWriter(Config _config)
        {
            if (_config.PublishType.Equals("file", StringComparison.InvariantCultureIgnoreCase))
            {
                return new FileWriterService(_config);
            }

            if (_config.PublishType.Equals("aws", StringComparison.InvariantCultureIgnoreCase))
            {
                return new S3WriterService(_config);
            }

            throw new ArgumentException("Unknown PublishType specified");
        }
    }
}
