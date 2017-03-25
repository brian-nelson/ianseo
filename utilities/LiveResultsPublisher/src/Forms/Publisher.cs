using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using LiveResultsPublisher.Objects;
using LiveResultsPublisher.Repos;
using LiveResultsPublisher.Services;
using Newtonsoft.Json;

namespace LiveResultsPublisher.Forms
{
    public partial class Publisher : Form
    {
        private Config m_currentConfig;
        private Database m_db;
        private QualificationScoresRepo m_scoresRepo;
        private TournamentRepo m_tournamentRepo;
        private S3WriterService m_S3;

        private bool m_done;
        private DateTime m_nextPublishTime;

        public Publisher()
        {
            InitializeComponent();
        }

        private void openToolStripMenuItem_Click(object sender, EventArgs e)
        {
            OpenFileDialog dialog = new OpenFileDialog
            {
                CheckFileExists = true,
                Multiselect = false,
                ShowReadOnly = false,
                Filter = "Configuration|*.json"
            };

            DialogResult result = dialog.ShowDialog();

            if (result == DialogResult.OK)
            {
                Initialize(dialog.FileName);
            }
        }

        private void Initialize(string configFileName)
        {
            try
            {
                var config = Config.Load(configFileName);
                string validationText = config.ValidateErrors();

                if (validationText == null)
                {
                    m_currentConfig = config;
                    competitionCode.Text = config.CompetitionCode;

                    m_db = new Database(m_currentConfig);
                    m_scoresRepo = new QualificationScoresRepo(m_db);
                    m_tournamentRepo = new TournamentRepo(m_db);
                    m_S3 = new S3WriterService(m_currentConfig);

                    GenerateNow.Enabled = true;
                    startPublishing.Enabled = true;
                }
                else
                {
                    MessageBox.Show($"Config file validation failed.\r\n{validationText}");
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Unable to open file.\n\r{ex.ToString()}");
            }
        }

        private void exitToolStripMenuItem_Click(object sender, EventArgs e)
        {
            Close();
        }

        private void PublishNow()
        {
            QualificationScoresReportService reportService = new QualificationScoresReportService(
                m_scoresRepo,
                m_tournamentRepo);

            var results = reportService.GetReportData(m_currentConfig.CompetitionCode);

            string json = JsonConvert.SerializeObject(results);
            m_S3.PublishFile("qualificationresults.json", json);

            if (InvokeRequired)
            {
                Invoke((MethodInvoker) delegate {
                    status.Text = $"Updated at {results.LastUpdated}";
                });
            }
            else
            {
                status.Text = $"Updated at {results.LastUpdated}";
            }
        }

        private void GenerateNow_Click(object sender, EventArgs e)
        {
            try
            {
                PublishNow();
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Unable to generate data.\n\r{ex.ToString()}");
            }
        }

        private void startPublishing_Click(object sender, EventArgs e)
        {
            m_done = false;
            m_nextPublishTime = DateTime.Now;

            startPublishing.Enabled = false;
            stopPublishing.Enabled = true;

            Thread t = new Thread(AutoPublish);
            t.Start();
        }

        private void stopPublishing_Click(object sender, EventArgs e)
        {
            m_done = true;
            stopPublishing.Enabled = false;
            startPublishing.Enabled = true;
        }

        public void AutoPublish()
        {
            do
            {
                try
                {
                    DateTime now = DateTime.Now;

                    if (now > m_nextPublishTime)
                    {
                        PublishNow();
                        m_nextPublishTime = DateTime.Now.AddMinutes(Convert.ToDouble(publishEvery.Value));
                    }
                }
                catch (Exception ex)
                {
                    
                }

            } while (!m_done);
        }
    }
}
