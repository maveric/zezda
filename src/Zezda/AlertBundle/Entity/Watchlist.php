<?php

namespace Zezda\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Watchlist
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Watchlist
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
     * @ORM\ManyToOne(targetEntity="Newsletter", inversedBy="watchlists")
     * @ORM\JoinColumn(name="newsletter", referencedColumnName="id")
     */
    private $newsletter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;

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
     * Set newsletter
     *
     * @param \Zezda\AlertBundle\Entity\Newsletter $newsletter
     * @return Watchlist
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
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return Watchlist
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
     * Set content
     *
     * @param string $content
     * @return Watchlist
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
