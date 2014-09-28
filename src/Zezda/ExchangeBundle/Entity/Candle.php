<?php

namespace Zezda\ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Candle
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zezda\ExchangeBundle\Entity\CandleRepository")
 * @ORM\Table(name="Candle", uniqueConstraints={@ORM\UniqueConstraint(name="idx2", columns={"stock", "datetime"})})
 */
class Candle
{
	/**
	 * @var integer
	 *
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Stock", inversedBy="candles")
	 * @ORM\JoinColumn(name="stock", referencedColumnName="id")
	 */
	private $stock;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="datetime", type="datetime")
	 */
	private $datetime;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="high", type="decimal", precision=8, scale=5)
	 */
	private $high;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="low", type="decimal", precision=8, scale=5)
	 */
	private $low;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="open", type="decimal", precision=8, scale=5)
	 */
	private $open;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="close", type="decimal", precision=8, scale=5)
	 */
	private $close;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="volume", type="integer")
	 */
	private $volume;


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
	 * Set stock
	 *
	 * @param integer $stock
	 *
	 * @return Candle
	 */
	public function setStock($stock)
	{
		$this->stock = $stock;

		return $this;
	}

	/**
	 * Get stock
	 *
	 * @return integer
	 */
	public function getStock()
	{
		return $this->stock;
	}

	/**
	 * Set datetime
	 *
	 * @param \DateTime $datetime
	 *
	 * @return Candle
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
	 * Set high
	 *
	 * @param string $high
	 *
	 * @return Candle
	 */
	public function setHigh($high)
	{
		$this->high = $high;

		return $this;
	}

	/**
	 * Get high
	 *
	 * @return string
	 */
	public function getHigh()
	{
		return $this->high;
	}

	/**
	 * Set low
	 *
	 * @param string $low
	 *
	 * @return Candle
	 */
	public function setLow($low)
	{
		$this->low = $low;

		return $this;
	}

	/**
	 * Get low
	 *
	 * @return string
	 */
	public function getLow()
	{
		return $this->low;
	}

	/**
	 * Set open
	 *
	 * @param string $open
	 *
	 * @return Candle
	 */
	public function setOpen($open)
	{
		$this->open = $open;

		return $this;
	}

	/**
	 * Get open
	 *
	 * @return string
	 */
	public function getOpen()
	{
		return $this->open;
	}

	/**
	 * Set close
	 *
	 * @param string $close
	 *
	 * @return Candle
	 */
	public function setClose($close)
	{
		$this->close = $close;

		return $this;
	}

	/**
	 * Get close
	 *
	 * @return string
	 */
	public function getClose()
	{
		return $this->close;
	}

	/**
	 * Set volume
	 *
	 * @param integer $volume
	 *
	 * @return Candle
	 */
	public function setVolume($volume)
	{
		$this->volume = $volume;

		return $this;
	}

	/**
	 * Get volume
	 *
	 * @return integer
	 */
	public function getVolume()
	{
		return $this->volume;
	}
}
