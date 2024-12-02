<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class Method
{

	private string $id;

	private string $name;

	private string $description;

	private string $logo;

	private string $group;

	private string $groupLabel;

	/**
	 * @param array<string, string> $methodData
	 * @return $this
	 */
	public function fromArray(array $methodData): self
	{
		$this->setId($methodData['id'])
			->setName($methodData['name'])
			->setDescription($methodData['description'])
			->setLogo($methodData['logo'])
			->setGroup($methodData['group'])
			->setGroupLabel($methodData['groupLabel']);

		return $this;
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return [
			'id'          => $this->getId(),
			'name'        => $this->getName(),
			'description' => $this->getDescription(),
			'logo'        => $this->getLogo(),
			'group'       => $this->getGroup(),
			'groupLabel'  => $this->getGroupLabel(),
		];
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

	public function getGroup(): string
	{
		return $this->group;
	}

	public function setGroup(string $group): Method
	{
		$this->group = $group;
		return $this;
	}

	public function getGroupLabel(): string
	{
		return $this->groupLabel;
	}

	public function setGroupLabel(string $groupLabel): Method
	{
		$this->groupLabel = $groupLabel;
		return $this;
	}

}
