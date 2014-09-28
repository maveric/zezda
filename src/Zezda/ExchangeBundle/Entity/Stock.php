<?php
/*
 * Alerts currently commented out while we manage the rest of the content
 */

namespace Zezda\ExchangeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Zezda\ExchangeBundle\Entity\StockRepository")
 * @ORM\Table(name="Stock", uniqueConstraints={@ORM\UniqueConstraint(name="idx", columns={"ticker", "exchange"})})
 */
class Stock
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
	 * @ORM\Column(name="ticker", type="string", length=25)
	 */
	private $ticker;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="exchange", type="string", length=25)
	 */
	private $exchange;

	/**
	 * @ORM\OneToMany(targetEntity="Zezda\AlertBundle\Entity\Alert", mappedBy="stock")
	 */
	protected $alerts;

	/**
	 * @ORM\OneToMany(targetEntity="Candle", mappedBy="stock")
	 */
	protected $candles;


	public function __construct()
	{
		$this->alerts = new ArrayCollection();
		$this->candles = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->getTicker();
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
	 * Set ticker
	 *
	 * @param string $ticker
	 *
	 * @return Stock
	 */
	public function setTicker($ticker)
	{
		$this->ticker = $ticker;

		return $this;
	}

	/**
	 * Get ticker
	 *
	 * @return string
	 */
	public function getTicker()
	{
		return $this->ticker;
	}

	/**
	 * Set exchange
	 *
	 * @param string $exchange
	 *
	 * @return Stock
	 */
	public function setExchange($exchange)
	{
		$this->exchange = $exchange;

		return $this;
	}

	/**
	 * Get exchange
	 *
	 * @return string
	 */
	public function getExchange()
	{
		return $this->exchange;
	}

	/**
	 * Add alerts
	 *
	 * @param \Zezda\AlertBundle\Entity\Alert $alerts
	 *
	 * @return Stock
	 */
	public function addAlert(\Zezda\AlertBundle\Entity\Alert $alerts)
	{
		$this->alerts[] = $alerts;

		return $this;
	}

	/**
	 * Remove alerts
	 *
	 * @param \Zezda\AlertBundle\Entity\Alert $alerts
	 */
	public function removeAlert(\Zezda\AlertBundle\Entity\Alert $alerts)
	{
		$this->alerts->removeElement($alerts);
	}

	/**
	 * Get alerts
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getAlerts()
	{
		return $this->alerts;
	}

	/**
	 * Add candles
	 *
	 * @param \Zezda\ExchangeBundle\Entity\Candle $candles
	 *
	 * @return Stock
	 */
	public function addCandle(\Zezda\ExchangeBundle\Entity\Candle $candles)
	{
		$this->candles[] = $candles;

		return $this;
	}

	/**
	 * Remove candles
	 *
	 * @param \Zezda\ExchangeBundle\Entity\Candle $candles
	 */
	public function removeCandle(\Zezda\ExchangeBundle\Entity\Candle $candles)
	{
		$this->candles->removeElement($candles);
	}

	/**
	 * Get candles
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCandles()
	{
		return $this->candles;
	}
}
