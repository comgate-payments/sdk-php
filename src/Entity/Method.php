<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class Method
{

	private string $id;

	private string $name;

	private string $description;

	private string $logo;

	public function fromArray(array $methodData)
	{
		$this->setId($methodData['id'])
			->setName($methodData['name'])
			->setDescription($methodData['description'])
			->setLogo($methodData['logo']);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return Method
	 */
	public function setId(string $id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Method
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return Method
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogo(): string
	{
		return $this->logo;
	}

	/**
	 * @param string $logo
	 * @return Method
	 */
	public function setLogo(string $logo): self
	{
		$this->logo = $logo;
		return $this;
	}

}
