namespace CFToIanseo.Forms
{
    partial class ConvertData
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.statusStrip1 = new System.Windows.Forms.StatusStrip();
            this.menuStrip1 = new System.Windows.Forms.MenuStrip();
            this.fileToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.openToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.exitToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.selectedFileLabel = new System.Windows.Forms.Label();
            this.selectedFile = new System.Windows.Forms.Label();
            this.outputFileLabel = new System.Windows.Forms.Label();
            this.outputFile = new System.Windows.Forms.TextBox();
            this.convert = new System.Windows.Forms.Button();
            this.menuStrip1.SuspendLayout();
            this.SuspendLayout();
            // 
            // statusStrip1
            // 
            this.statusStrip1.Location = new System.Drawing.Point(0, 157);
            this.statusStrip1.Name = "statusStrip1";
            this.statusStrip1.Size = new System.Drawing.Size(728, 22);
            this.statusStrip1.TabIndex = 0;
            this.statusStrip1.Text = "statusStrip1";
            // 
            // menuStrip1
            // 
            this.menuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.fileToolStripMenuItem});
            this.menuStrip1.Location = new System.Drawing.Point(0, 0);
            this.menuStrip1.Name = "menuStrip1";
            this.menuStrip1.Size = new System.Drawing.Size(728, 24);
            this.menuStrip1.TabIndex = 1;
            this.menuStrip1.Text = "menuStrip1";
            // 
            // fileToolStripMenuItem
            // 
            this.fileToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.openToolStripMenuItem,
            this.exitToolStripMenuItem});
            this.fileToolStripMenuItem.Name = "fileToolStripMenuItem";
            this.fileToolStripMenuItem.Size = new System.Drawing.Size(37, 20);
            this.fileToolStripMenuItem.Text = "File";
            // 
            // openToolStripMenuItem
            // 
            this.openToolStripMenuItem.Name = "openToolStripMenuItem";
            this.openToolStripMenuItem.Size = new System.Drawing.Size(152, 22);
            this.openToolStripMenuItem.Text = "Open";
            this.openToolStripMenuItem.Click += new System.EventHandler(this.openToolStripMenuItem_Click);
            // 
            // exitToolStripMenuItem
            // 
            this.exitToolStripMenuItem.Name = "exitToolStripMenuItem";
            this.exitToolStripMenuItem.Size = new System.Drawing.Size(152, 22);
            this.exitToolStripMenuItem.Text = "Exit";
            this.exitToolStripMenuItem.Click += new System.EventHandler(this.exitToolStripMenuItem_Click);
            // 
            // selectedFileLabel
            // 
            this.selectedFileLabel.Location = new System.Drawing.Point(12, 39);
            this.selectedFileLabel.Name = "selectedFileLabel";
            this.selectedFileLabel.Size = new System.Drawing.Size(77, 17);
            this.selectedFileLabel.TabIndex = 2;
            this.selectedFileLabel.Text = "Selected File:";
            // 
            // selectedFile
            // 
            this.selectedFile.Location = new System.Drawing.Point(98, 39);
            this.selectedFile.Name = "selectedFile";
            this.selectedFile.Size = new System.Drawing.Size(618, 17);
            this.selectedFile.TabIndex = 3;
            // 
            // outputFileLabel
            // 
            this.outputFileLabel.Location = new System.Drawing.Point(12, 70);
            this.outputFileLabel.Name = "outputFileLabel";
            this.outputFileLabel.Size = new System.Drawing.Size(77, 17);
            this.outputFileLabel.TabIndex = 4;
            this.outputFileLabel.Text = "Output File:";
            // 
            // outputFile
            // 
            this.outputFile.Location = new System.Drawing.Point(98, 70);
            this.outputFile.Name = "outputFile";
            this.outputFile.Size = new System.Drawing.Size(618, 20);
            this.outputFile.TabIndex = 5;
            // 
            // convert
            // 
            this.convert.Location = new System.Drawing.Point(594, 96);
            this.convert.Name = "convert";
            this.convert.Size = new System.Drawing.Size(122, 23);
            this.convert.TabIndex = 6;
            this.convert.Text = "Convert to IANSEO";
            this.convert.UseVisualStyleBackColor = true;
            // 
            // ConvertData
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(728, 179);
            this.Controls.Add(this.convert);
            this.Controls.Add(this.outputFile);
            this.Controls.Add(this.outputFileLabel);
            this.Controls.Add(this.selectedFile);
            this.Controls.Add(this.selectedFileLabel);
            this.Controls.Add(this.statusStrip1);
            this.Controls.Add(this.menuStrip1);
            this.MainMenuStrip = this.menuStrip1;
            this.Name = "ConvertData";
            this.Text = "ConvertData";
            this.menuStrip1.ResumeLayout(false);
            this.menuStrip1.PerformLayout();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.StatusStrip statusStrip1;
        private System.Windows.Forms.MenuStrip menuStrip1;
        private System.Windows.Forms.ToolStripMenuItem fileToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem openToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem exitToolStripMenuItem;
        private System.Windows.Forms.Label selectedFileLabel;
        private System.Windows.Forms.Label selectedFile;
        private System.Windows.Forms.Label outputFileLabel;
        private System.Windows.Forms.TextBox outputFile;
        private System.Windows.Forms.Button convert;
    }
}