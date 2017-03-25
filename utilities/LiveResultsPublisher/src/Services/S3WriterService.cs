using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Amazon;
using Amazon.Runtime;
using Amazon.S3;
using Amazon.S3.Model;
using LiveResultsPublisher.Objects;

namespace LiveResultsPublisher.Services
{
    public class S3WriterService
    {
        private Config m_config;

        public S3WriterService(Config _config)
        {
            m_config = _config;
        }

        private RegionEndpoint GetRegion(string regionName)
        {
            switch (regionName)
            {
                case "us-east-1":
                    return RegionEndpoint.USEast1;
                case "us-east-2":
                    return RegionEndpoint.USEast2;
                case "us-west-1":
                    return RegionEndpoint.USWest1;
                case "us-west-2":
                    return RegionEndpoint.USWest2;
            }

            throw new NotSupportedException("Specified AWS Region is not supported");
        }

        public void PublishFile(string filename, string body)
        {
            BasicAWSCredentials credentials = new BasicAWSCredentials(m_config.AwsKey, m_config.AwsSecret);

            AmazonS3Client s3 = new AmazonS3Client(credentials, GetRegion(m_config.AwsRegion));

            PutObjectRequest request = new PutObjectRequest
            {
                BucketName = m_config.AwsBucket,
                Key = m_config.AwsFolder + "/" + filename,
                ContentBody = body
            };

            var response = s3.PutObject(request);
        }
    }
}
