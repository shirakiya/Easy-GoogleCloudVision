<?php

class FileManager
{
	protected $_uploadedFile;
	protected $_file;
	protected $_uniqFilename;
	protected $_filePath;
	protected $_fileType;

	protected $_allowedMimeType = array(
		'image/gif',
		'image/png',
		'image/jpeg',
	);

	/**
	 * @param UploadedFileInterface $file
	 */
	public function __construct($file)
	{
		$this->_uploadedFile = $file;
		$this->_checkConfirmUploadFile();
		$this->_setFile();
	}

	public function getUploadedFile()
	{
		return $this->_uploadedFile;
	}

	public function getFile()
	{
		return $this->_file;
	}

	/**
	 * check upload file correctly
	 * @return void
	 */
	protected function _checkConfirmUploadFile()
	{
		switch ($this->_uploadedFile->getError()) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new Exception('Empty File Upload ERROR!');
				break;
			case UPLOAD_ERR_FORM_SIZE;
			case UPLOAD_ERR_INI_SIZE;
				throw new Exception('Too Large File Size ERROR!');
				break;
			default:
				throw new Exception('Something ERROR!');
				break;
		}
	}

	/**
	 * set file content instance variable
	 * @return void
	 */
	protected function _setFile()
	{
		$this->_moveToImageDir();

		if (!$this->_validateMimeType()) {
			unlink($this->getFilePath());  // delete file
			throw new Exception('Not Allowed MIME Type File ERROR!');
		}

		$this->_file = file_get_contents($this->getFilePath());
	}

	/**
	 * validate uploaded file's mime type
	 * @return bool
	 */
	protected function _validateMimeType()
	{
		$this->_fileType = \MimeType\MimeType::getType($this->getFilePath());

		if (!in_array($this->_fileType, $this->_allowedMimeType)) {
			return false;
		}
		return true;
	}

	/**
	 * get unique file name for put application directory
	 * @return string
	 */
	public function getUniqFilename()
	{
		if (empty($this->_uniqFilename)) {
			$this->_uniqFilename = sprintf('%s_%s', uniqid(), $this->_uploadedFile->getClientFilename());
		}

		return $this->_uniqFilename;
	}

	public function getFilePath()
	{
		if (empty($this->_filePath)) {
			$imageDir = __DIR__.'/../images/';
			$this->_filePath = $imageDir.$this->getUniqFilename();
		}

		return $this->_filePath;
	}

	/**
	 * move to images directory for save file
	 */
	protected function _moveToImageDir()
	{
		$path = $this->getFilePath();
		$this->_uploadedFile->moveTo($path);
		chmod($path, 0644);
	}

	public function getFileURL()
	{
		return '/images/'.$this->getUniqFilename();
	}
}
