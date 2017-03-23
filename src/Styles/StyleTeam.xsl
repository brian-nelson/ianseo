<?xml version="1.0" encoding="UTF-8"?>

<!-- 
	Usato per i team abs e non nelle qualifiche
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
    
    <!-- Qui carico la lista delle squadre -->
    <xsl:apply-templates select="Team" />
</xsl:template>

<!-- 
	Per ogni squadra
 -->
<xsl:template match="Team">
    <xsl:for-each select="Athlete">
    	<tr>
    		<xsl:if test="count(parent::*/preceding-sibling::*) mod 2=0">
    			<xsl:attribute name="class">
	    			yellow
	    		</xsl:attribute>
    		</xsl:if>
    		
    		<xsl:if test="position()=1">
		    	<td>
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="../@Quanti"/>
		    		</xsl:attribute>
		    		
		    		<xsl:value-of select="../@Rank"/>
		    	</td>
		    	
		    	<td>
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="../@Quanti"/>
		    		</xsl:attribute>
		    		
		    		<xsl:value-of select="../@NationCode"/>
		    	</td>  	
		    	
		    	<td>
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="../@Quanti"/>
		    		</xsl:attribute>
		    		
		    		<xsl:value-of select="../@Nation"/>
		    	</td>
	    	</xsl:if>
	    	
	    	<td><xsl:value-of select="@Name"/></td>
	    	<td><xsl:value-of select="@Division"/></td>
	    	<td><xsl:value-of select="@AgeClass"/></td>
	    	<td><xsl:value-of select="@Class"/></td>
	    	<td><xsl:value-of select="@SubClass"/></td>
	    	<td><xsl:value-of select="@QuScore"/></td>
	    	
	    	<xsl:if test="position()=1">
		    	<td>
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="../@Quanti"/>
		    		</xsl:attribute>
		    		
		    		<xsl:value-of select="../@Total"/>
		    	</td>
		    	
		    	<td>
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="../@Quanti"/>
		    		</xsl:attribute>
		    		
		    		<xsl:value-of select="../@Gold"/>
		    	</td>
		    	
		    	<td>
		    		<xsl:attribute name="rowspan">
		    			<xsl:value-of select="../@Quanti"/>
		    		</xsl:attribute>
		    		
		    		<xsl:value-of select="../@Xnine"/>
		    	</td>
		    	
		    	<xsl:if test="../@SO">
		    		<td>
		    			<xsl:attribute name="rowspan">
			    			<xsl:value-of select="../@Quanti"/>
			    		</xsl:attribute>
		    		
		    			<xsl:value-of select="../@SO"/>
		    		</td>
		    	</xsl:if>
	    	</xsl:if>
    	</tr>
    </xsl:for-each>
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