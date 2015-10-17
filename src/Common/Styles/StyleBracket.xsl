<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- 
	Il template results contiene i risultati, è la root
 -->

<xsl:template match="Results">
  <html>
  <head>
  	<link href="/Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css" />
  </head>
	  <body>
	  	<input type="hidden" id="ts" value="">
	  		<xsl:attribute name="value"><xsl:value-of select="@TS" /></xsl:attribute>
	  	</input>
		<table class="Tabella">
			<!-- Da qui applico i successivi template che si verificano -->
			<xsl:apply-templates />
	    </table>		
	  </body>
  </html>
</xsl:template>

<!-- La lista è un evento -->
<xsl:template match="List">
	<!-- Titolo dell'evento -->
	<tr class="Main">
		<td class="Center">
			<xsl:attribute name="colspan">
				<xsl:value-of select="@Columns"/>
			</xsl:attribute>
			<xsl:value-of select="@Title" />
		</td>
	</tr>
	<!-- Qui verrà applicato il template delle fasi -->
	<xsl:apply-templates select="Phase" />
</xsl:template>

<!-- fase -->
<xsl:template match="Phase">
	<!--  titolo della fase -->
	<tr>
		<th>
			<xsl:attribute name="colspan">
				<xsl:value-of select="@Columns"/>
			</xsl:attribute>
			-- <xsl:value-of select="@Title" /> --
		</th>
	</tr>
	
	<!-- Applico Caption -->
	<tr> 
		<xsl:apply-templates select="Caption" />
	</tr>
	<!-- applico Match -->	
	<xsl:for-each select="Match">
		<tr>
			<xsl:if test="position() mod 2 = 0">
				<xsl:attribute name="class">
					yellow
				</xsl:attribute>
			</xsl:if>
			
			<td>
				<xsl:if test="Athlete[1]/@Win=1">
					<xsl:attribute name="class">
						win
					</xsl:attribute>
				</xsl:if>
				
				<xsl:if test="Athlete[1]/Name">
					<xsl:value-of select="Athlete[1]/Name" /><br/>
				</xsl:if>
				<xsl:value-of select="Athlete[1]/Country" />
			</td>
			<td>
				<xsl:if test="Athlete[2]/@Win=1">
					<xsl:attribute name="class">
						win
					</xsl:attribute>
				</xsl:if>
				
				<xsl:if test="Athlete[2]/Name">
					<xsl:value-of select="Athlete[2]/Name" /><br/>
				</xsl:if>
				<xsl:value-of select="Athlete[2]/Country" />
			</td>
			
			<td>
				<xsl:value-of select="Athlete[1]/Score" /><br/>
				<xsl:value-of select="Athlete[1]/Tiebreak" />
			</td>
			<td>
				<xsl:value-of select="Athlete[2]/Score" /><br/>
				<xsl:value-of select="Athlete[2]/Tiebreak" />
			</td>
		</tr>	
	</xsl:for-each>

	<tr class="Divider">
    	<td>
    		<xsl:attribute name="colspan">
    			<xsl:value-of select="@Columns"/>
    		</xsl:attribute>
    	</td>
    </tr>
</xsl:template>

<xsl:template match="Caption">
	<th>
		<xsl:attribute name="colspan">
			<xsl:value-of select="@Columns"/>
		</xsl:attribute>
		
		<xsl:value-of select="text()" />
	</th>
</xsl:template>

</xsl:stylesheet>