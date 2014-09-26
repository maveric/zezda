<?php

namespace Zezda\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alert
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zezda\AlertBundle\Entity\AlertRepository")
 */
class Alert
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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="datetime", type="datetime")
	 */
	private $datetime;

	/**
	 * @ORM\ManyToOne(targetEntity="AlertType", inversedBy="alerts")
	 */
	private $type;

	/**
	 * @ORM\ManyToOne(targetEntity="AlertContent", inversedBy="alerts", cascade={"persist"})
	 */
	private $content;

	/**
	 * @ORM\ManyToOne(targetEntity="Newsletter", inversedBy="alerts")
	 */
	private $newsletter;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="price", type="decimal", precision=8, scale=5)
	 */
	private $price;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="short", type="boolean")
	 */
	private $short;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="buy", type="boolean")
	 */

	private $buy;

	/**
	 * @param boolean $isBuy
	 */
	public function setBuy($isBuy)
	{
		$this->buy = $isBuy;
	}

	/**
	 * @return boolean
	 */
	public function getBuy()
	{
		return $this->buy;
	}

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="shares", type="integer")
	 */
	private $shares;

	/**
	 * @ORM\ManyToOne(targetEntity="Zezda\ExchangeBundle\Entity\Stock", inversedBy="alerts")
	 */
	private $stock;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="approved", type="boolean")
	 */
	private $approved;

	/**
	 * @param boolean $approved
	 */
	public function setApproved($approved)
	{
		$this->approved = $approved;
	}

	/**
	 * @return boolean
	 */
	public function getApproved()
	{
		return $this->approved;
	}


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
	 * Set datetime
	 *
	 * @param \DateTime $datetime
	 *
	 * @return Alert
	 */
	public function setDatetime($datetime)
	{
		$this->datetime = $datetime;

		return $this;
	}

	/**
	 * Get datetime
	 *
	 * @return \DateTime
	 */
	public function getDatetime()
	{
		return $this->datetime;
	}

	/**
	 * Set type
	 *
	 * @param integer $type
	 *
	 * @return Alert
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return integer
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set content
	 *
	 * @param \Zezda\AlertBundle\Entity\AlertContent $content
	 *
	 * @return Alert
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return \Zezda\AlertBundle\Entity\AlertContent
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set newsletter
	 *
	 * @param \Zezda\AlertBundle\Entity\Newsletter $newsletter
	 *
	 * @return Alert
	 */
	public function setNewsletter($newsletter)
	{
		$this->newsletter = $newsletter;

		return $this;
	}

	/**
	 * Get newsletter
	 *
	 * @return \Zezda\AlertBundle\Entity\Newsletter
	 */
	public function getNewsletter()
	{
		return $this->newsletter;
	}

	/**
	 * Set price
	 *
	 * @param string $price
	 *
	 * @return Alert
	 */
	public function setPrice($price)
	{
		$this->price = $price;

		return $this;
	}

	/**
	 * Get price
	 *
	 * @return string
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * Set action
	 *
	 * @param boolean $action
	 *
	 * @return Alert
	 */
	public function setShort($action)
	{
		$this->short = $action;

		return $this;
	}

	/**
	 * Get action
	 *
	 * @return boolean
	 */
	public function getShort()
	{
		return $this->short;
	}

	/**
	 * Set shares
	 *
	 * @param integer $shares
	 *
	 * @return Alert
	 */
	public function setShares($shares)
	{
		$this->shares = $shares;

		return $this;
	}

	/**
	 * Get shares
	 *
	 * @return integer
	 */
	public function getShares()
	{
		return $this->shares;
	}

	/**
	 * Set stock
	 *
	 * @param \Zezda\ExchangeBundle\Entity\Stock $stock
	 *
	 * @return Alert
	 */
	public function setStock($stock)
	{
		$this->stock = $stock;

		return $this;
	}

	/**
	 * Get stock
	 *
	 * @return \Zezda\ExchangeBundle\Entity\Stock
	 */
	public function getStock()
	{
		return $this->stock;
	}
}
