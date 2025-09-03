<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity;

class Method
{

	private string $id;

	private string $group;

	private string $groupLabel;

	private string $name;
	private string $name_short;

	private string $description;
	private string $logo;
	private string $logo_240;
	private string $cblogo;
	private string $clogo;
	private string $sblogo;
	private string $slogo;

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
			->setGroupLabel($methodData['groupLabel'])
			->setNameShort($methodData['name_short'] ?? '')
			->setLogo240($methodData['logo_240'] ?? '')
			->setCblogo($methodData['logo_240c'] ?? '')
			->setClogo($methodData['logo_120c'] ?? '')
			->setSblogo($methodData['logo_150s'] ?? '')
			->setSlogo($methodData['logo_100s'] ?? '');

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
			'name_short'  => $this->getNameShort(),
			'logo_240'    => $this->getLogo240(),
			'cblogo'      => $this->getCblogo(),
			'clogo'       => $this->getClogo(),
			'sblogo'      => $this->getSblogo(),
			'slogo'       => $this->getSlogo(),
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

	/**
	 * @return string
	 */
	public function getGroup(): string
	{
		return $this->group;
	}

	/**
	 * @param string $group
	 * @return $this
	 */
	public function setGroup(string $group): Method
	{
		$this->group = $group;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGroupLabel(): string
	{
		return $this->groupLabel;
	}

	/**
	 * @param string $groupLabel
	 * @return $this
	 */
	public function setGroupLabel(string $groupLabel): Method
	{
		$this->groupLabel = $groupLabel;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNameShort(): string
	{
		return $this->name_short;
	}

	/**
	 * @param string $name_short
	 * @return self
	 */
	public function setNameShort(string $name_short): self
	{
		$this->name_short = $name_short;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogo240(): string
	{
		return $this->logo_240;
	}

	/**
	 * @param string $logo_240
	 * @return self
	 */
	public function setLogo240(string $logo_240): self
	{
		$this->logo_240 = $logo_240;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCblogo(): string
	{
		return $this->cblogo;
	}

	/**
	 * @param string $cblogo
	 * @return self
	 */
	public function setCblogo(string $cblogo): self
	{
		$this->cblogo = $cblogo;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClogo(): string
	{

		return $this->clogo;
	}

	/**
	 * @param string $clogo
	 * @return self
	 */
	public function setClogo(string $clogo): self
	{
		$this->clogo = $clogo;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSblogo(): string
	{
		return $this->sblogo;
	}

	/**
	 * @param string $sblogo
	 * @return self
	 */
	public function setSblogo(string $sblogo): self
	{
		$this->sblogo = $sblogo;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSlogo(): string
	{
		return $this->slogo;
	}

	/**
	 * @param string $slogo
	 * @return self
	 */
	public function setSlogo(string $slogo): self
	{
		$this->slogo = $slogo;
		return $this;
	}
}
