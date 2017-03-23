<?php
/*

ods-php a library to read and write ods files from php.

This library has been forked from eyeOS project and licended under the LGPL3
terms available at: http://www.gnu.org/licenses/lgpl-3.0.txt (relicenced
with permission of the copyright holders)

Copyright: Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org) - 2008

https://sourceforge.net/projects/ods-php/

Specs: http://docs.oasis-open.org/office/v1.2/os/OpenDocument-v1.2-os-part1.html

*/

/**
 * Enter description here ...
 * @author deligant
 *
 */
class ods {
	var $fonts;
	var $styles;
	var $sheets;
	var $lastElement;
	var $fods;
	var $currentSheet;
	var $currentRow;
	var $currentCell;
	var $lastRowAtt;
	var $repeat;
	var $maxCols=0;
	var $rowStyles=array();
	var $colStyles=array();

	function __construct() {
		$this->styles = array();
		$this->fonts = array();
		$this->sheets = array();
		$this->currentRow = 0;
		$this->currentSheet = 0;
		$this->currentCell = 0;
		$this->repeat = 0;
	}

	function parse($data) {
		$xml_parser = xml_parser_create();
		xml_set_object ( $xml_parser, $this );
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");

		xml_parse($xml_parser, $data, strlen($data));

		xml_parser_free($xml_parser);
	}

	function array2ods() {
		$fontArray = $this->fonts;
		$styleArray = $this->styles;
		$sheetArray = $this->sheets;
		// Header
		$string = '<?xml version="1.0" encoding="UTF-8"?>'
			. '<office:document-content '
				. 'xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" '
				. 'xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" '
				. 'xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" '
				. 'xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" '
				. 'xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" '
				. 'xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" '
				. 'xmlns:xlink="http://www.w3.org/1999/xlink" '
				. 'xmlns:dc="http://purl.org/dc/elements/1.1/" '
				. 'xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" '
				. 'xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" '
				. 'xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" x'
				. 'mlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" '
				. 'xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" '
				. 'xmlns:math="http://www.w3.org/1998/Math/MathML" '
				. 'xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" '
				. 'xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" '
				. 'xmlns:ooo="http://openoffice.org/2004/office" '
				. 'xmlns:ooow="http://openoffice.org/2004/writer" '
				. 'xmlns:oooc="http://openoffice.org/2004/calc" '
				. 'xmlns:dom="http://www.w3.org/2001/xml-events" '
				. 'xmlns:xforms="http://www.w3.org/2002/xforms" '
				. 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
				. 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
				. 'office:version="1.0">';

		// ToDo: scripts
		$string .= '<office:scripts/>';

		// Fonts
		$string .= '<office:font-face-decls>';
		foreach ($fontArray as $fontName => $fontAttribs) {
			$string .= '<style:font-face ';
			foreach ($fontAttribs as $attrName => $attrValue) {
				$string .= strtolower($attrName) . '="' . $attrValue . '" ';
			}
			$string .= '/>';
		}
		$string .= '</office:font-face-decls>';

		// Styles
		$string .= '<office:automatic-styles>';
		foreach ($styleArray as $styleName => $styleAttribs) {
			$string .= '<style:style ';
			foreach ($styleAttribs['attrs'] as $attrName => $attrValue) {
				$string .= strtolower($attrName) . '="' . $attrValue . '" ';
			}
			$string .= '>';

			// Subnodes
			foreach ($styleAttribs['styles'] as $nodeName => $nodeTree) {
				$string .= '<' . $nodeName . ' ';
				foreach ($nodeTree as $attrName => $attrValue) {
					$string .= strtolower($attrName) . '="' . $attrValue . '" ';
				}
				$string .= '/>';
			}

			$string .= '</style:style>';
		}
		$string .= '</office:automatic-styles>';

		// Body
		$string .= '<office:body>';
		$string .= '<office:spreadsheet>';
		foreach ($sheetArray as $tableIndex => $tableContent) {
			$string .= '<table:table table:name="' . $tableIndex . '" table:print="false">';
			//$string .= '<office:forms form:automatic-focus="false" form:apply-design-mode="false"/>';
			if($this->colStyles and !empty($this->colStyles[$tableIndex])) {
				$n=0;
				$OldStyle='';
				$Columns=0;
				foreach($this->colStyles[$tableIndex] as $col => $style) {
					if($n<$col) {
						$num=$col-$n;
						$string.= '<table:table-column '
							. ($num>1 ? 'table:number-columns-repeated="'.$num.'" ' : '')
							. '/>';
						$n=$col;
					}
					$string.= '<table:table-column ';
					foreach($style as $attrName => $attrValue) {
						$string .= strtolower($attrName) . '="' . $attrValue . '" ';
					}
					$string.= '/>';
					$n+=(empty($style['table:number-columns-repeated']) ? 1 : $style['table:number-columns-repeated']);
				}
			}
			foreach(range(0, max(array_keys($tableContent['rows']))) as $n) {
				$string .= '<table:table-row ';
				if(!empty($this->rowStyles[$tableIndex][$n])) {
					foreach($this->rowStyles[$tableIndex][$n] as $attrName => $attrValue) {
						$string .= strtolower($attrName) . '="' . $attrValue . '" ';
					}
				}
				$string .='>';
				if(!empty($tableContent['rows'][$n])) {
					foreach(range(0, max(array_keys($tableContent['rows'][$n]))) as $o) {
						$string .= '<table:table-cell ';
						if(!empty($tableContent['rows'][$n][$o])) {
							foreach ($tableContent['rows'][$n][$o]['attrs'] as $attrName => $attrValue) {
								$string .= strtolower($attrName) . '="' . str_replace('"', '&quot;', $attrValue) . '" ';
							}
							$string .= '>';

							if(isset($tableContent['rows'][$n][$o]['value'])) {
								$string .= '<text:p>' . $tableContent['rows'][$n][$o]['value'] . '</text:p>';
							}
						} else {
							$string .= '>';
						}
						$string .= '</table:table-cell>';
					}
				}
				$string .= '</table:table-row>';
			}
			$string .= '</table:table>';
		}

		$string .= '</office:spreadsheet>';
		$string .= '</office:body>';

		// Footer
		$string .= '</office:document-content>';


// 		die($string);
		return $string;
	}

	function startElement($parser, $tagName, $attrs) {
		$cTagName = strtolower($tagName);
		if($cTagName == 'style:font-face') {
			$this->fonts[$attrs['STYLE:NAME']] = $attrs;
		} elseif($cTagName == 'style:style') {
			$this->lastElement = $attrs['STYLE:NAME'];
			$this->styles[$this->lastElement]['attrs'] = $attrs;
		} elseif($cTagName == 'style:table-column-properties' || $cTagName == 'style:table-row-properties'
			|| $cTagName == 'style:table-properties' || $cTagName == 'style:text-properties') {
			$this->styles[$this->lastElement]['styles'][$cTagName] = $attrs;
		} elseif($cTagName == 'table:table-cell') {
			$this->lastElement = $cTagName;
			$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$this->currentCell]['attrs'] = $attrs;
			if(isset($attrs['TABLE:NUMBER-COLUMNS-REPEATED'])) {
				$times = intval($attrs['TABLE:NUMBER-COLUMNS-REPEATED']);
				$times--;
				for($i=1;$i<=$times;$i++) {
					$cnum = $this->currentCell+$i;
					$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$cnum]['attrs'] = $attrs;
				}
				$this->currentCell += $times;
				$this->repeat = $times;
			}
			if(isset($this->lastRowAtt['TABLE:NUMBER-ROWS-REPEATED'])) {
				$times = intval($this->lastRowAtt['TABLE:NUMBER-ROWS-REPEATED']);
				$times--;
				for($i=1;$i<=$times;$i++) {
					$cnum = $this->currentRow+$i;
					$this->sheets[$this->currentSheet]['rows'][$cnum][$i-1]['attrs'] = $attrs;
				}
				$this->currentRow += $times;
			}
		} elseif($cTagName == 'table:table-row') {
			$this->lastRowAtt = $attrs;
		}
	}

	function endElement($parser, $tagName) {
		$cTagName = strtolower($tagName);
		if($cTagName == 'table:table') {
			$this->currentSheet++;
			$this->currentRow = 0;
		} elseif($cTagName == 'table:table-row') {
			$this->currentRow++;
			$this->currentCell = 0;
		} elseif($cTagName == 'table:table-cell') {
			$this->currentCell++;
			$this->repeat = 0;
		}
	}

	function characterData($parser, $data) {
		if($this->lastElement == 'table:table-cell') {
			$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$this->currentCell]['value'] = $data;
			if($this->repeat > 0) {
				for($i=0;$i<$this->repeat;$i++) {
					$cnum = $this->currentCell - ($i+1);
					$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$cnum]['value'] = $data;
				}
			}
		}
	}

	function getMeta($lang) {
		$myDate = date('Y-m-j\TH:i:s');
		$meta = '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
			<office:meta>
				<meta:generator>ods-php</meta:generator>
				<meta:creation-date>'.$myDate.'</meta:creation-date>
				<dc:date>'.$myDate.'</dc:date>
				<dc:language>'.$lang.'</dc:language>
				<meta:editing-cycles>2</meta:editing-cycles>
				<meta:editing-duration>PT15S</meta:editing-duration>
				<meta:user-defined meta:name="Info 1"/>
				<meta:user-defined meta:name="Info 2"/>
				<meta:user-defined meta:name="Info 3"/>
				<meta:user-defined meta:name="Info 4"/>
			</office:meta>
		</office:document-meta>';
		return $meta;
	}

	function getStyle() {
		return '<?xml version="1.0" encoding="UTF-8"?>
			<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" office:version="1.0"><office:font-face-decls><style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:styles><style:default-style style:family="table-cell"><style:table-cell-properties style:decimal-places="2"/><style:paragraph-properties style:tab-stop-distance="1.25cm"/><style:text-properties style:font-name="Liberation Sans" fo:language="es" fo:country="ES" style:font-name-asian="DejaVu Sans" style:language-asian="zxx" style:country-asian="none" style:font-name-complex="DejaVu Sans" style:language-complex="zxx" style:country-complex="none"/></style:default-style><number:number-style style:name="N0"><number:number number:min-integer-digits="1"/>
			</number:number-style><number:currency-style style:name="N103P0" style:volatile="true"><number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true"/><number:text> </number:text><number:currency-symbol number:language="es" number:country="ES">€</number:currency-symbol></number:currency-style><number:currency-style style:name="N103"><style:text-properties fo:color="#ff0000"/><number:text>-</number:text><number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true"/><number:text> </number:text><number:currency-symbol number:language="es" number:country="ES">€</number:currency-symbol><style:map style:condition="value()&gt;=0" style:apply-style-name="N103P0"/></number:currency-style><style:style style:name="Default" style:family="table-cell"/><style:style style:name="Result" style:family="table-cell" style:parent-style-name="Default"><style:text-properties fo:font-style="italic" style:text-underline-style="solid" style:text-underline-width="auto" style:text-underline-color="font-color" fo:font-weight="bold"/></style:style><style:style style:name="Result2" style:family="table-cell" style:parent-style-name="Result" style:data-style-name="N103"/><style:style style:name="Heading" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center"/><style:text-properties fo:font-size="16pt" fo:font-style="italic" fo:font-weight="bold"/></style:style><style:style style:name="Heading1" style:family="table-cell" style:parent-style-name="Heading"><style:table-cell-properties style:rotation-angle="90"/></style:style></office:styles><office:automatic-styles><style:page-layout style:name="pm1"><style:page-layout-properties style:writing-mode="lr-tb"/><style:header-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-bottom="0.25cm"/></style:header-style><style:footer-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-top="0.25cm"/>
			</style:footer-style></style:page-layout><style:page-layout style:name="pm2"><style:page-layout-properties style:writing-mode="lr-tb"/><style:header-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-bottom="0.25cm" fo:border="0.088cm solid #000000" fo:padding="0.018cm" fo:background-color="#c0c0c0"><style:background-image/></style:header-footer-properties></style:header-style><style:footer-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-top="0.25cm" fo:border="0.088cm solid #000000" fo:padding="0.018cm" fo:background-color="#c0c0c0"><style:background-image/></style:header-footer-properties></style:footer-style></style:page-layout></office:automatic-styles><office:master-styles><style:master-page style:name="Default" style:page-layout-name="pm1"><style:header><text:p><text:sheet-name>???</text:sheet-name></text:p></style:header><style:header-left style:display="false"/><style:footer><text:p>Página <text:page-number>1</text:page-number></text:p></style:footer><style:footer-left style:display="false"/></style:master-page><style:master-page style:name="Report" style:page-layout-name="pm2"><style:header><style:region-left><text:p><text:sheet-name>???</text:sheet-name> (<text:title>???</text:title>)</text:p></style:region-left><style:region-right><text:p><text:date style:data-style-name="N2" text:date-value="2008-02-18">18/02/2008</text:date>, <text:time>00:17:06</text:time></text:p></style:region-right></style:header><style:header-left style:display="false"/><style:footer><text:p>Página <text:page-number>1</text:page-number> / <text:page-count>99</text:page-count></text:p></style:footer><style:footer-left style:display="false"/></style:master-page></office:master-styles></office:document-styles>';
	}

	function getSettings() {
		return '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-settings xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0"><office:settings><config:config-item-set config:name="ooo:view-settings"><config:config-item config:name="VisibleAreaTop" config:type="int">0</config:config-item><config:config-item config:name="VisibleAreaLeft" config:type="int">0</config:config-item><config:config-item config:name="VisibleAreaWidth" config:type="int">2258</config:config-item><config:config-item config:name="VisibleAreaHeight" config:type="int">903</config:config-item><config:config-item-map-indexed config:name="Views"><config:config-item-map-entry><config:config-item config:name="ViewId" config:type="string">View1</config:config-item><config:config-item-map-named config:name="Tables"><config:config-item-map-entry config:name="Hoja1"><config:config-item config:name="CursorPositionX" config:type="int">0</config:config-item><config:config-item config:name="CursorPositionY" config:type="int">1</config:config-item><config:config-item config:name="HorizontalSplitMode" config:type="short">0</config:config-item><config:config-item config:name="VerticalSplitMode" config:type="short">0</config:config-item><config:config-item config:name="HorizontalSplitPosition" config:type="int">0</config:config-item><config:config-item config:name="VerticalSplitPosition" config:type="int">0</config:config-item><config:config-item config:name="ActiveSplitRange" config:type="short">2</config:config-item><config:config-item config:name="PositionLeft" config:type="int">0</config:config-item><config:config-item config:name="PositionRight" config:type="int">0</config:config-item><config:config-item config:name="PositionTop" config:type="int">0</config:config-item><config:config-item config:name="PositionBottom" config:type="int">0</config:config-item></config:config-item-map-entry></config:config-item-map-named><config:config-item config:name="ActiveTable" config:type="string">Hoja1</config:config-item><config:config-item config:name="HorizontalScrollbarWidth" config:type="int">270</config:config-item><config:config-item config:name="ZoomType" config:type="short">0</config:config-item><config:config-item config:name="ZoomValue" config:type="int">100</config:config-item><config:config-item config:name="PageViewZoomValue" config:type="int">60</config:config-item><config:config-item config:name="ShowPageBreakPreview" config:type="boolean">false</config:config-item><config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item><config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item><config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item><config:config-item config:name="GridColor" config:type="long">12632256</config:config-item><config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item><config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item><config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item><config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item><config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item><config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item><config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item><config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item><config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item>
		<config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item><config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item></config:config-item-map-entry></config:config-item-map-indexed></config:config-item-set><config:config-item-set config:name="ooo:configuration-settings"><config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item><config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item><config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item><config:config-item config:name="GridColor" config:type="long">12632256</config:config-item><config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item><config:config-item config:name="LinkUpdateMode" config:type="short">3</config:config-item><config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item><config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item><config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item><config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item><config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item><config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item><config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item><config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item><config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item><config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item><config:config-item config:name="AutoCalculate" config:type="boolean">true</config:config-item><config:config-item config:name="PrinterName" config:type="string">Generic Printer</config:config-item><config:config-item config:name="PrinterSetup" config:type="base64Binary">WAH+/0dlbmVyaWMgUHJpbnRlcgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU0dFTlBSVAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWAAMAngAAAAAAAAAFAFZUAAAkbQAASm9iRGF0YSAxCnByaW50ZXI9R2VuZXJpYyBQcmludGVyCm9yaWVudGF0aW9uPVBvcnRyYWl0CmNvcGllcz0xCm1hcmdpbmRhanVzdG1lbnQ9MCwwLDAsMApjb2xvcmRlcHRoPTI0CnBzbGV2ZWw9MApjb2xvcmRldmljZT0wClBQRENvbnRleERhdGEKUGFnZVNpemU6TGV0dGVyAAA=</config:config-item><config:config-item config:name="ApplyUserData" config:type="boolean">true</config:config-item><config:config-item config:name="CharacterCompressionType" config:type="short">0</config:config-item><config:config-item config:name="IsKernAsianPunctuation" config:type="boolean">false</config:config-item><config:config-item config:name="SaveVersionOnClose" config:type="boolean">false</config:config-item><config:config-item config:name="UpdateFromTemplate" config:type="boolean">false</config:config-item><config:config-item config:name="AllowPrintJobCancel" config:type="boolean">true</config:config-item><config:config-item config:name="LoadReadonly" config:type="boolean">false</config:config-item></config:config-item-set></office:settings></office:document-settings>';
	}

	function getManifest() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
 <manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/statusbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/accelerator/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/floater/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/popupmenu/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/progressbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/menubar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/toolbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/Bitmaps/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/"/>
 <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
</manifest:manifest>';
	}

	function addCell($sheet,$row,$cell,$value,$type) {
		$this->sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
		$this->setCellAttribute('OFFICE:VALUE-TYPE', $type, $row, $cell);
		$this->setCellAttribute('OFFICE:VALUE', $value, $row, $cell);
	}

	function editCell($sheet,$row,$cell,$value) {
		$this->setCellAttribute('OFFICE:VALUE', $value, $row, $cell);
		$this->sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
	}

	function setActiveSheet($Sheet, $Cols=0) {
		$this->currentSheet=$Sheet;
		$this->currentRow=0;
		$this->currentCell=0;
		if($Cols) $this->maxCols=$Cols;
	}

	function Cell($value, $type='string', $row=null, $cell=null, $raw=false) {
		if(is_null($row)) {
			$row=$this->currentRow;
		} elseif(!$raw) {
			$this->currentRow=$row;
			$this->currentCell=0;
		}
		if(is_null($cell)) {
			$cell=$this->currentCell;
		} elseif(!$raw) {
			$this->currentCell=$cell;
		}
		$this->setCellAttribute('OFFICE:VALUE-TYPE', $type, $row, $cell);
		$this->setCellAttribute('OFFICE:VALUE', $value, $row, $cell);
		$this->sheets[$this->currentSheet]['rows'][$row][$cell]['value'] = $value;
		$this->currentCell++;
		if($this->maxCols>0 and $this->currentCell>$this->maxCols) {
			$this->currentRow++;
			$this->currentCell=0;
		}
	}

	/**
	 * setStyle() ...
	 * @param string $Name
	 * name of the style to use in cells, rows or columns
	 * @param array $styles
	 * array of node arrays. nodes can be: 'style:text-properties', 'style:table-cell-properties'. styles of nodes: 'fo:font-weight', 'fo:color', 'fo:background-color' (table-cell!)
	 * @param array $attrs
	 * array of style-families: 'table-cell', 'table-column', 'table-row', 'table'
	 */

	function setStyle($Name, $styles=array(), $attrs=array('style:family'=>'table-cell')) {
		$this->styles[$Name]['attrs']['style:name']=$Name;
		foreach($attrs as $k => $v) $this->styles[$Name]['attrs'][$k]=$v;

		foreach($styles as $Node => $a) foreach($a as $k => $v) {
			if($v) {
				$this->styles[$Name]['styles'][$Node][$k]=$v;
				switch($k) {
					case 'fo:font-weight':
						$this->styles[$Name]['styles'][$Node]['style:font-weight-asian']=$v;
						$this->styles[$Name]['styles'][$Node]['style:font-weight-complex']=$v;
						break;
					case 'fo:font-style':
						$this->styles[$Name]['styles'][$Node]['style:font-style-asian']=$v;
						$this->styles[$Name]['styles'][$Node]['style:font-style-complex']=$v;
						break;
				}
			} else {
				unset($this->styles[$Name]['styles'][$Node][$k]);
				switch($k) {
					case 'fo:font-weight':
						unset($this->styles[$Name]['styles'][$Node]['style:font-weight-asian']);
						unset($this->styles[$Name]['styles'][$Node]['style:font-weight-complex']);
						break;
					case 'fo:font-style':
						unset($this->styles[$Name]['styles'][$Node]['style:font-style-asian']);
						unset($this->styles[$Name]['styles'][$Node]['style:font-style-complex']);
						break;
				}
			}
		}
	}

	function setCellStyle($Name, $row=null, $cell=null) {
		if(empty($this->styles[$Name])) return;

		$this->setCellAttribute('table:style-name', $Name, $row, $cell);
	}


	/**
	 * Sets cell attributes ...
	 * @param string $Name name of the attribute to set/unset: 'table:number-columns-spanned'
	 * @param string $Value value of the attribute:if null then the attribute is unset
	 * @param integer $row
	 * @param integer $cell
	 */
	function setCellAttribute($Name, $Value, $row=null, $cell=null) {
		if(is_null($row)) $row=$this->currentRow;
		if(is_null($cell)) $cell=$this->currentCell;
		if(is_null($Value)) {
			unset($this->sheets[$this->currentSheet]['rows'][$row][$cell]['attrs'][$Name]);
		} else {
			$this->sheets[$this->currentSheet]['rows'][$row][$cell]['attrs'][$Name]=$Value;
		}
	}

	function setRowStyle($Name, $row=null) {
		if(empty($this->styles[$Name])) return;

		$this->setRowAttribute('table:style-name', $Name, $row);
	}

	function setColStyle($Name, $col=null, $Columns=1) {
		if(empty($this->styles[$Name])) return;

		$this->setColAttribute('table:style-name', $Name, $col);
		$this->setColAttribute('table:number-columns-repeated', $Columns, $col);
	}

	function setRowAttribute($Name, $Value, $row=null) {
		if(is_null($row)) $row=$this->currentRow;
		if(is_null($Value)) {
			unset($this->rowStyles[$this->currentSheet][$row][$Name]);
		} else {
			$this->rowStyles[$this->currentSheet][$row][$Name]=$Value;
		}
	}

	function setColAttribute($Name, $Value, $col=null) {
		if(is_null($col)) $col=$this->currentCell;
		if(is_null($Value)) {
			unset($this->colStyles[$this->currentSheet][$col][$Name]);
		} else {
			$this->colStyles[$this->currentSheet][$col][$Name]=$Value;
		}
	}

	function addRow($row=array()) {
		if(!is_array($row)) $row=array($row);
		$Cell=$this->currentCell;
		foreach($row as $item) $this->cell($item);
		$this->currentRow++;
		$this->currentCell=$Cell;
	}

	/**
	 * Enter description here ...
	 * @param string $file
	 * @param string $out default to 'a' (attachment), can also be 'f' (file) or 'i' (inline)
	 */
	function save($file='SpreadSheet.ods', $out='a') {
		$zip = new ZipArchive();

		$filename = tempnam("/tmp", "SpreadSheet.ods");

		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
		    exit("cannot open <$filename>\n");
		}

		$zip->addFromString("content.xml", $this->array2ods());
		$zip->addFromString('mimetype','application/vnd.oasis.opendocument.spreadsheet');
		$zip->addFromString('meta.xml',$this->getMeta('it'));
		$zip->addFromString('styles.xml',$this->getStyle());
		$zip->addFromString('settings.xml',$this->getSettings());
		$zip->addFromString('META-INF/manifest.xml',$this->getManifest());
		$zip->addEmptyDir('Configurations2');
		$zip->addEmptyDir('Configurations2/acceleator');
		$zip->addEmptyDir('Configurations2/images');
		$zip->addEmptyDir('Configurations2/popupmenu');
		$zip->addEmptyDir('Configurations2/statusbar');
		$zip->addEmptyDir('Configurations2/floater');
		$zip->addEmptyDir('Configurations2/menubar');
		$zip->addEmptyDir('Configurations2/progressbar');
		$zip->addEmptyDir('Configurations2/toolbar');

		$zip->close();

		if($out!='f') {
			header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
			if($out=='a') header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
			readfile($filename);
			die();
		}
		rename($filename, $file);
	}

	function oldsave($file='/tmp/temp.ods', $out='a') {
		if($file[0]!='/') $file = '/tmp/'.$file;

		$charset = ini_get('default_charset');
		ini_set('default_charset', 'UTF-8');
		$tmp = $this->get_tmp_dir();
		$uid = uniqid();
		mkdir($tmp.'/'.$uid);
		file_put_contents($tmp.'/'.$uid.'/content.xml',$this->array2ods());
		file_put_contents($tmp.'/'.$uid.'/mimetype','application/vnd.oasis.opendocument.spreadsheet');
		file_put_contents($tmp.'/'.$uid.'/meta.xml',$this->getMeta('es-ES'));
		file_put_contents($tmp.'/'.$uid.'/styles.xml',$this->getStyle());
		file_put_contents($tmp.'/'.$uid.'/settings.xml',$this->getSettings());
		mkdir($tmp.'/'.$uid.'/META-INF/');
		mkdir($tmp.'/'.$uid.'/Configurations2/');
		mkdir($tmp.'/'.$uid.'/Configurations2/accelerator/');
		mkdir($tmp.'/'.$uid.'/Configurations2/images/');
		mkdir($tmp.'/'.$uid.'/Configurations2/popupmenu/');
		mkdir($tmp.'/'.$uid.'/Configurations2/statusbar/');
		mkdir($tmp.'/'.$uid.'/Configurations2/floater/');
		mkdir($tmp.'/'.$uid.'/Configurations2/menubar/');
		mkdir($tmp.'/'.$uid.'/Configurations2/progressbar/');
		mkdir($tmp.'/'.$uid.'/Configurations2/toolbar/');
		file_put_contents($tmp.'/'.$uid.'/META-INF/manifest.xml',$this->getManifest());
		shell_exec('cd '.$tmp.'/'.$uid.';zip -r '.escapeshellarg($file).' ./');
		ini_set('default_charset',$charset);

		if($out!='a') return;

		header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
		header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
		readfile($file);
	}

	function get_tmp_dir() {
		$path = '';
		if(!function_exists('sys_get_temp_dir')){
			$path = $this->try_get_temp_dir();
		}else{
			$path = sys_get_temp_dir();
			if(is_dir($path)){
				return $path;
			}else{
				$path = $this->try_get_temp_dir();
			}
		}
		return $path;
	}

	function try_get_temp_dir() {
	    // Try to get from environment variable
		if(!empty($_ENV['TMP'])){
			$path = realpath($_ENV['TMP']);
		}else if(!empty($_ENV['TMPDIR'])){
			$path = realpath( $_ENV['TMPDIR'] );
		}else if(!empty($_ENV['TEMP'])){
			$path = realpath($_ENV['TEMP']);
		}
		// Detect by creating a temporary file
		else{
			// Try to use system's temporary directory
			// as random name shouldn't exist
			$temp_file = tempnam(md5(uniqid(rand(),TRUE)),'');
			if ($temp_file){
				$temp_dir = realpath(dirname($temp_file));
				unlink($temp_file);
				$path = $temp_dir;
			}else{
				return "/tmp";
			}
		}
		return $path;
	}
}

function parseOds($file) {
	$tmp = get_tmp_dir();
	copy($file,$tmp.'/'.basename($file));
	$path = $tmp.'/'.basename($file);
	$uid = uniqid();
	mkdir($tmp.'/'.$uid);
	shell_exec('unzip '.escapeshellarg($path).' -d '.escapeshellarg($tmp.'/'.$uid));
	$obj = new ods();
	$obj->parse(file_get_contents($tmp.'/'.$uid.'/content.xml'));
	return $obj;
}


function newOds() {
	$content = '<?xml version="1.0" encoding="UTF-8"?>
	<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">
	<office:scripts/>
	<office:font-face-decls><style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/>
	<style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles>
	<style:style style:name="co1" style:family="table-column">
	<style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style>
	<style:style style:name="ro1" style:family="table-row">
	<style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style>
	<style:style style:name="ta1" style:family="table" style:master-page-name="Default">
	<style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style></office:automatic-styles>
	<office:body><office:spreadsheet>
	<table:table table:name="Hoja1" table:style-name="ta1" table:print="false">
	<office:forms form:automatic-focus="false" form:apply-design-mode="false"/>
	<table:table-column table:style-name="co1" table:default-cell-style-name="Default"/>
	<table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table>
	<table:table table:name="Hoja2" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table>
	<table:table table:name="Hoja3" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table>
	</office:spreadsheet></office:body></office:document-content>';
	$obj = new ods();
	$obj->parse($content);
	return $obj;
}


