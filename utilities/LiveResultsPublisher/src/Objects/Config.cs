using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;

namespace LiveResultsPublisher.Objects
{
    public class Config
    {
        public string ConnectionString { get; set; }

        public string PublishType { get; set; }

        public string Folder { get; set; }

        public string AwsKey { get; set; }

        public string AwsSecret { get; set; }

        public string AwsRegion { get; set; }

        public string AwsBucket { get; set; }

        public string AwsFolder { get; set; }

        public string CompetitionCode { get; set; }


        public static Config Load(string filename)
        {
            string json = File.ReadAllText(filename);

            return JsonConvert.DeserializeObject<Config>(json);
        }

        public string ValidateErrors()
        {
            StringBuilder sb = new StringBuilder();

            if (ConnectionString == null)
            {
                sb.AppendLine("Connection String is missing");
            }

            if (CompetitionCode == null)
            {
                sb.AppendLine("Competition Code is missing");
            }

            if (PublishType == null)
            {
                sb.AppendLine("PublishType is missing");
            }
            else if (PublishType.Equals("aws", StringComparison.InvariantCultureIgnoreCase))
            {
                if (AwsBucket == null)
                {
                    sb.AppendLine("AWS Bucket is Missing");
                }

                if (AwsFolder == null)
                {
                    sb.AppendLine("AWS Folder is missing");
                }

                if (AwsKey == null)
                {
                    sb.AppendLine("AWS Key is missing");
                }

                if (AwsRegion == null)
                {
                    sb.AppendLine("AWS Region is missing");
                }

                if (AwsSecret == null)
                {
                    sb.AppendLine("AWS Secret is missing");
                }
            }
            else if (PublishType.Equals("file", StringComparison.InvariantCultureIgnoreCase))
            {
                if (Folder == null)
                {
                    sb.AppendLine("Folder is missing");
                }
            }
            
            if (sb.Length > 0)
            {
                return sb.ToString();
            }

            return null;
        }
    }
}
