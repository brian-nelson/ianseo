<?php

/**
 * Class FontMeta based on several stackoverflow answers.
 *
 * @method string getCopyright()
 * @method string getFontFamily()
 * @method string getFontSubFamily()
 * @method string getFontIdentifier()
 * @method string getFontName()
 * @method string getFontVersion()
 * @method string getPostscriptName()
 * @method string getTrademark()
 * @method string getManufacturerName()
 * @method string getDesigner()
 * @method string getDescription()
 * @method string getVendorUrl()
 * @method string getDesignerUrl()
 * @method string getLicenseDescription()
 * @method string getLicenseUrl()
 * @method string getReservedField()
 * @method string getPreferredFamily()
 * @method string getPreferredSubFamily()
 * @method string getCompatibleFullName()
 * @method string getPostscriptCid()
 */
class FontMeta
{
	/**
	 * @var string
	 */
	private $fileName;

	/**
	 * @var array
	 */
	private $data = array(
		'copyright' => null,
		'fontFamily' => null,
		'fontSubFamily' => null,
		'fontIdentifier' => null,
		'fontName' => null,
		'fontVersion' => null,
		'postscriptName' => null,
		'trademark' => null,
		'manufacturerName' => null,
		'designer' => null,
		'description' => null,
		'vendorUrl' => null,
		'designerUrl' => null,
		'licenseDescription' => null,
		'licenseUrl' => null,
		'reservedField' => null,
		'preferredFamily' => null,
		'preferredSubFamily' => null,
		'compatibleFullName' => null,
		'postscriptCid' => null,
	);

	/**
	 * FontMeta constructor.
	 *
	 * @param string $fileName
	 * @throws Exception
	 */
	function __construct($fileName = '')
	{
		if (!$fileName || !is_readable($fileName)) {
			throw new Exception('File ' . $fileName . ' is not readable');
		}

		$this->fileName = $fileName;
		$this->readFontMetadata();
	}

	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * @param string $name
	 * @param array  $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		$property = lcfirst(substr($name, 3));
		return isset($this->data[$property]) ? $this->data[$property] : null;
	}

	/**
	 *  Read the font Metadata
	 *
	 * @throws Exception
	 */
	public function readFontMetadata()
	{
		/** @var resource $fontHandle */
		$fontHandle = fopen($this->fileName, "rb");

		//  Read the file header
		$TT_OFFSET_TABLE = fread($fontHandle, 12);

		$uMajorVersion = $this->unpackShort(substr($TT_OFFSET_TABLE, 0, 2));
		$uMinorVersion = $this->unpackShort(substr($TT_OFFSET_TABLE, 2, 2));
		$uNumOfTables = $this->unpackShort(substr($TT_OFFSET_TABLE, 4, 2));

		//  Check is this is a true type font and the version is 1.0
		if ($uMajorVersion != 1 || $uMinorVersion != 0) {
			fclose($fontHandle);
			throw new Exception($this->fileName . ' is not a Truetype font file');
		}

		//  Look for details of the name table
		$nameTableFound = false;
		for ($t = 0; $t < $uNumOfTables; $t++) {
			$TT_TABLE_DIRECTORY = fread($fontHandle, 16);
			$szTag = substr($TT_TABLE_DIRECTORY, 0, 4);
			if (strtolower($szTag) == 'name') {
				$uOffset = $this->unpackLong(substr($TT_TABLE_DIRECTORY, 8, 4));
				$nameTableFound = true;
				break;
			}
		}

		if (!$nameTableFound) {
			fclose($fontHandle);
			throw new Exception('Can\'t find name table in ' . $this->fileName);
		}

		//  Set offset to the start of the name table
		fseek($fontHandle, $uOffset, SEEK_SET);

		$TT_NAME_TABLE_HEADER = fread($fontHandle, 6);

		$uNRCount = $this->unpackShort(substr($TT_NAME_TABLE_HEADER, 2, 2));
		$uStorageOffset = $this->unpackShort(substr($TT_NAME_TABLE_HEADER, 4, 2));

		for ($a = 0; $a < $uNRCount; $a++) {
			$TT_NAME_RECORD = fread($fontHandle, 12);

			$uNameID = $this->unpackShort(substr($TT_NAME_RECORD, 6, 2));
			if ($uNameID <= count($this->data)) {
				$uStringLength = $this->unpackShort(substr($TT_NAME_RECORD, 8, 2));
				$uStringOffset = $this->unpackShort(substr($TT_NAME_RECORD, 10, 2));

				if ($uStringLength > 0) {
					$nPos = ftell($fontHandle);
					fseek($fontHandle, $uOffset + $uStringOffset + $uStorageOffset, SEEK_SET);

					$testValue = fread($fontHandle, $uStringLength);
					$this->extractCandidate($uNameID, $testValue);

					fseek($fontHandle, $nPos, SEEK_SET);
				}
			}
		}

		fclose($fontHandle);
	}

	/**
	 *  Convert a big-endian word value to an integer.
	 *
	 * @param string $value
	 * @return int
	 */
	private function unpackShort($value)
	{
		return $this->unpack($value, 'n');
	}

	/**
	 *  Convert a big-endian word value to an integer.
	 *
	 * @param string $value
	 * @return int
	 */
	private function unpackLong($value)
	{
		return $this->unpack($value, 'N');
	}

	/**
	 *  Convert a big-endian word or longword value to an integer.
	 *
	 * @param string $value
	 * @param string $format
	 * @return int
	 */
	private function unpack($value, $format = 'n')
	{
		$unpacked = unpack($format, $value);
		return (int)array_pop($unpacked);
	}

	/**
	 * Extract possible property/attribute. $data keys are
	 * intentionally sorted in order of the font file table.
	 *
	 * @param int    $uNameId
	 * @param string $testValue
	 */
	private function extractCandidate($uNameId, $testValue)
	{
		$map = array_keys($this->data);
		if (strlen(trim($testValue))
			&& array_key_exists($uNameId, $map)
		) {
			$this->data[$map[$uNameId]] = $this->cleanupValue(trim($testValue));
		}
	}

	/**
	 * Remove ASCII ctrl character 00
	 *
	 * @param string $value
	 * @return string
	 */
	private function cleanupValue($value)
	{
		return trim(str_replace(chr(00), '', $value));
	}
}
