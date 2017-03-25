namespace LiveResultsPublisher.Forms
{
    partial class Publisher
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
            this.menuStrip1 = new System.Windows.Forms.MenuStrip();
            this.fileToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.openToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.exitToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.competitionCodeLabel = new System.Windows.Forms.Label();
            this.competitionCode = new System.Windows.Forms.Label();
            this.GenerateNow = new System.Windows.Forms.Button();
            this.statusStrip1 = new System.Windows.Forms.StatusStrip();
            this.status = new System.Windows.Forms.ToolStripStatusLabel();
            this.panel1 = new System.Windows.Forms.Panel();
            this.startPublishing = new System.Windows.Forms.Button();
            this.stopPublishing = new System.Windows.Forms.Button();
            this.publishEvery = new System.Windows.Forms.NumericUpDown();
            this.label1 = new System.Windows.Forms.Label();
            this.menuStrip1.SuspendLayout();
            this.statusStrip1.SuspendLayout();
            this.panel1.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.publishEvery)).BeginInit();
            this.SuspendLayout();
            // 
            // menuStrip1
            // 
            this.menuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.fileToolStripMenuItem});
            this.menuStrip1.Location = new System.Drawing.Point(0, 0);
            this.menuStrip1.Name = "menuStrip1";
            this.menuStrip1.Size = new System.Drawing.Size(379, 24);
            this.menuStrip1.TabIndex = 0;
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
            // competitionCodeLabel
            // 
            this.competitionCodeLabel.Location = new System.Drawing.Point(12, 40);
            this.competitionCodeLabel.Name = "competitionCodeLabel";
            this.competitionCodeLabel.Size = new System.Drawing.Size(100, 23);
            this.competitionCodeLabel.TabIndex = 1;
            this.competitionCodeLabel.Text = "Competition Code";
            // 
            // competitionCode
            // 
            this.competitionCode.Location = new System.Drawing.Point(118, 40);
            this.competitionCode.Name = "competitionCode";
            this.competitionCode.Size = new System.Drawing.Size(100, 23);
            this.competitionCode.TabIndex = 2;
            // 
            // GenerateNow
            // 
            this.GenerateNow.Enabled = false;
            this.GenerateNow.Location = new System.Drawing.Point(246, 35);
            this.GenerateNow.Name = "GenerateNow";
            this.GenerateNow.Size = new System.Drawing.Size(101, 23);
            this.GenerateNow.TabIndex = 3;
            this.GenerateNow.Text = "Publish Now";
            this.GenerateNow.UseVisualStyleBackColor = true;
            this.GenerateNow.Click += new System.EventHandler(this.GenerateNow_Click);
            // 
            // statusStrip1
            // 
            this.statusStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.status});
            this.statusStrip1.Location = new System.Drawing.Point(0, 157);
            this.statusStrip1.Name = "statusStrip1";
            this.statusStrip1.Size = new System.Drawing.Size(379, 22);
            this.statusStrip1.TabIndex = 4;
            this.statusStrip1.Text = "statusStrip1";
            // 
            // status
            // 
            this.status.Name = "status";
            this.status.Size = new System.Drawing.Size(0, 17);
            // 
            // panel1
            // 
            this.panel1.Controls.Add(this.label1);
            this.panel1.Controls.Add(this.publishEvery);
            this.panel1.Controls.Add(this.stopPublishing);
            this.panel1.Controls.Add(this.startPublishing);
            this.panel1.Location = new System.Drawing.Point(15, 67);
            this.panel1.Name = "panel1";
            this.panel1.Size = new System.Drawing.Size(352, 76);
            this.panel1.TabIndex = 5;
            // 
            // startPublishing
            // 
            this.startPublishing.Enabled = false;
            this.startPublishing.Location = new System.Drawing.Point(231, 10);
            this.startPublishing.Name = "startPublishing";
            this.startPublishing.Size = new System.Drawing.Size(101, 23);
            this.startPublishing.TabIndex = 0;
            this.startPublishing.Text = "Start";
            this.startPublishing.UseVisualStyleBackColor = true;
            this.startPublishing.Click += new System.EventHandler(this.startPublishing_Click);
            // 
            // stopPublishing
            // 
            this.stopPublishing.Enabled = false;
            this.stopPublishing.Location = new System.Drawing.Point(231, 39);
            this.stopPublishing.Name = "stopPublishing";
            this.stopPublishing.Size = new System.Drawing.Size(101, 23);
            this.stopPublishing.TabIndex = 1;
            this.stopPublishing.Text = "Stop";
            this.stopPublishing.UseVisualStyleBackColor = true;
            this.stopPublishing.Click += new System.EventHandler(this.stopPublishing_Click);
            // 
            // publishEvery
            // 
            this.publishEvery.Location = new System.Drawing.Point(13, 39);
            this.publishEvery.Maximum = new decimal(new int[] {
            10,
            0,
            0,
            0});
            this.publishEvery.Minimum = new decimal(new int[] {
            1,
            0,
            0,
            0});
            this.publishEvery.Name = "publishEvery";
            this.publishEvery.Size = new System.Drawing.Size(120, 20);
            this.publishEvery.TabIndex = 2;
            this.publishEvery.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
            // 
            // label1
            // 
            this.label1.Location = new System.Drawing.Point(10, 10);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(123, 23);
            this.label1.TabIndex = 6;
            this.label1.Text = "Auto Publish";
            // 
            // Publisher
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(379, 179);
            this.Controls.Add(this.panel1);
            this.Controls.Add(this.statusStrip1);
            this.Controls.Add(this.GenerateNow);
            this.Controls.Add(this.competitionCode);
            this.Controls.Add(this.competitionCodeLabel);
            this.Controls.Add(this.menuStrip1);
            this.MainMenuStrip = this.menuStrip1;
            this.Name = "Publisher";
            this.Text = "Publisher";
            this.menuStrip1.ResumeLayout(false);
            this.menuStrip1.PerformLayout();
            this.statusStrip1.ResumeLayout(false);
            this.statusStrip1.PerformLayout();
            this.panel1.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.publishEvery)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.MenuStrip menuStrip1;
        private System.Windows.Forms.ToolStripMenuItem fileToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem openToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem exitToolStripMenuItem;
        private System.Windows.Forms.Label competitionCodeLabel;
        private System.Windows.Forms.Label competitionCode;
        private System.Windows.Forms.Button GenerateNow;
        private System.Windows.Forms.StatusStrip statusStrip1;
        private System.Windows.Forms.ToolStripStatusLabel status;
        private System.Windows.Forms.Panel panel1;
        private System.Windows.Forms.NumericUpDown publishEvery;
        private System.Windows.Forms.Button stopPublishing;
        private System.Windows.Forms.Button startPublishing;
        private System.Windows.Forms.Label label1;
    }
}