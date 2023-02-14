<?php

namespace Comgate\SDK\Entity\Response;

use Comgate\SDK\Exception\LogicalException;

class FileResponse
{
	protected string $filename = '';

	protected string $fileContent;

	public function saveToFile($directory, $fileName = '')
	{
		if (empty($fileName)) {
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
