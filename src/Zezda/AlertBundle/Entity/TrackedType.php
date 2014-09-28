<?php

namespace Zezda\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrackedType
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TrackedType
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=25)
	 */
	private $type;

	/**
	 * @ORM\ManyToMany(targetEntity="Newsletter", mappedBy="tracked_types")
	 */
	private $newsletters;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return TrackedType
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	public function __toString()
	{
		return $this->getType();
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->newsletters = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Add newsletters
	 *
	 * @param \Zezda\AlertBundle\Entity\Newsletter $newsletters
	 *
	 * @return TrackedType
	 */
	public function addNewsletter(\Zezda\AlertBundle\Entity\Newsletter $newsletters)
	{
		$this->newsletters[] = $newsletters;

		return $this;
	}

	/**
	 * Remove newsletters
	 *
	 * @param \Zezda\AlertBundle\Entity\Newsletter $newsletters
	 */
	public function removeNewsletter(\Zezda\AlertBundle\Entity\Newsletter $newsletters)
	{
		$this->newsletters->removeElement($newsletters);
	}

	/**
	 * Get newsletters
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getNewsletters()
	{
		return $this->newsletters;
	}
}
