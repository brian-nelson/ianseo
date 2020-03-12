<?xml version="1.0" encoding="UTF-8"?>

<!-- 
	Usato per gli individuali abs e non nelle qualifiche
-->

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

<!-- 
	List rappresenta una serie, ad esempio Arco Olimpico Maschile nelle qualificazioni 
 -->
<xsl:template match="List">
	<tr class="Main">
		<td class="Center">
			<xsl:attribute name="colspan">
				<xsl:value-of select="@Columns"/>
			</xsl:attribute>
			<xsl:value-of select="@Title" />
		</td>
	</tr>
	<tr>
		<!-- Qui devo generare le colonne della lista usando il template Caption-->
		<xsl:apply-templates select="Caption" />
    </tr>
    
    <!-- Qui carico la lista degli atleti -->
    <xsl:apply-templates select="Athlete" />
    <tr class="Divider">
    	<td>
    		<xsl:attribute name="colspan">
    			<xsl:value-of select="@Columns"/>
    		</xsl:attribute>
    	</td>
    </tr>
</xsl:template>

<!-- 
	Per ogni atleta (sia ind che in team)
 -->
<xsl:template match="Athlete">
    <tr>	
    	<xsl:if test="position() mod 2 = 0">
    		<xsl:attribute name="class">
    			yellow
    		</xsl:attribute>
    	</xsl:if>
    	
    	<!-- Gli Item sono le celle -->
    	<xsl:for-each select="Item">
	    	<td>
	    		<!--  se esiste l'attributo rows lo uso per il rowspan -->
	    		<xsl:if test="@Rows">
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="@Rows"/>
		    		</xsl:attribute>
		    	</xsl:if>	
		    	
		    	<!--  se esiste l'attributo Columns lo uso per il colspan -->
		    	<xsl:if test="@Columns">
		    		<xsl:attribute name="colspan">
		    			<xsl:value-of select="@Columns"/>
		    		</xsl:attribute>
		    	</xsl:if>	
		    	
	    		<xsl:value-of select="text()" />
	    	</td>			
    	</xsl:for-each>
    </tr>
</xsl:template>

<!-- 
	Serve a produrre le colonne della lista
 -->
<xsl:template match="Caption">
	<th>
		<xsl:attribute name="colspan">
			<xsl:value-of select="@Columns"/>
		</xsl:attribute>
		<xsl:value-of select="text()" />
	</th>
</xsl:template>


</xsl:stylesheet>