<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\LogicalException;

class FileResponse
{
	/**
	 * @var string
	 */
	protected $filename = '';
	/**
	 * @var string
	 */
	protected $fileContent;

    /**
     * @param string $directory
     * @param string $fileName
     * @return void
     */
	public function saveToFile(string $directory, string $fileName = '')
	{
		if (strlen($fileName) === 0) {
			$fileName = $this->getFilename();
		}

		if (!is_dir($directory) || !is_writable($directory)) {
			throw new LogicalException("Path {$directory} is not a directory or is not writable");
		}

		file_put_contents($directory . DIRECTORY_SEPARATOR . $fileName, $this->getFileContent());
	}

	/**
	 * @return string
	 */
	public function getFilename(): string
	{
		return $this->filename;
	}

	/**
	 * @param string $filename
	 * @return self
	 */
	public function setFilename(string $filename): self
	{
		$this->filename = $filename;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileContent(): string
	{
		return $this->fileContent;
	}

	/**
	 * @param string $fileContent
	 * @return self
	 */
	public function setFileContent(string $fileContent): self
	{
		$this->fileContent = $fileContent;
		return $this;
	}
}
