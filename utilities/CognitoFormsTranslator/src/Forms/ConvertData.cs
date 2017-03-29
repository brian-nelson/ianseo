using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace CFToIanseo.Forms
{
    public partial class ConvertData : Form
    {
        private string m_Filename;

        public ConvertData()
        {
            InitializeComponent();
        }

        private void openToolStripMenuItem_Click(object sender, EventArgs e)
        {
            OpenFileDialog ofd = new OpenFileDialog
            {
                CheckFileExists = true,
                Filter = "Excel|*.xlsx",
                Multiselect = false
            };

            DialogResult result = ofd.ShowDialog();
            if (result == DialogResult.OK)
            {
                m_Filename = ofd.FileName;
                selectedFile.Text = m_Filename;

                string path = Path.GetDirectoryName(m_Filename);
                string file = Path.GetFileNameWithoutExtension(m_Filename) + "_ianseo.tab";

                outputFile.Text = Path.Combine(path, file);
            }
        }

        private void exitToolStripMenuItem_Click(object sender, EventArgs e)
        {
            Close();
        }
    }
}
