<?php

namespace Zezda\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlertContent
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AlertContent
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
	 * @ORM\Column(name="content", type="text")
	 */
	private $content;


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
	 * Set content
	 *
	 * @param string $content
	 *
	 * @return AlertContent
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
}
