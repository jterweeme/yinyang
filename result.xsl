<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <html>
            <head>
                <title>Onzin</title>
            </head>
            <body>
                <xsl:apply-templates select="result"/>
            </body>
        </html>
    </xsl:template>
    <xsl:template match="result">
        <h1>Result</h1>
        <h2><xsl:value-of select="user"/></h2>
        <h3><xsl:value-of select="score"/></h3>
        <div><xsl:apply-templates select="exercise"/></div>
    </xsl:template>
    <xsl:template match="exercise">
        <div class="vraag"><b><xsl:value-of select="vraag"/></b></div>
        <div><xsl:apply-templates select="choice"/></div>
        <xsl:apply-templates select="toelichting"/>
    </xsl:template>
    <xsl:template match="choice">
        <xsl:for-each select="item">
            <p>
                <input type="radio" disabled="true">
                    <xsl:if test="@checked">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label>
                    <xsl:if test="@checked">
                        <xsl:attribute name="class">rood</xsl:attribute>
                    </xsl:if>
                    <xsl:if test="@goed">
                        <xsl:attribute name="class">groen</xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="."/>
                </label>
            </p>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>


