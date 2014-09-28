<?php

namespace Zezda\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Newsletter
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
     * @ORM\Column(name="name", type="string", length=75)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=100)
     */
    private $site;

    /**
     * @var integer
     *
     * @ORM\Column(name="affiliate_id", type="integer")
     */
    private $affiliateId;

    /**
     * @ORM\ManyToMany(targetEntity="AlertType", inversedBy="newsletters")
     */
    private $types;

    /**
     * @ORM\ManyToMany(targetEntity="TrackedType", inversedBy="newsletters")
     */
    private $tracked_types;

    /**
     * @var string
     *
     * @ORM\Column(name="guru_name", type="string", length=50)
     */

    private $guruName;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_prefix", type="string", length=25)
     */

    private $smsPrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="email_from_field", type="string", length=50)
     */

    private $emailFromField;

    /**
     * @ORM\OneToMany(targetEntity="Watchlist", mappedBy="newsletter")
     */
    private $watchlists;

    /**
     * @param string $emailFromField
     */
    public function setEmailFromField($emailFromField)
    {
        $this->emailFromField = $emailFromField;
    }

    /**
     * @return string
     */
    public function getEmailFromField()
    {
        return $this->emailFromField;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_profitly", type="boolean")
     */

    private $isProfitly;

    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return Newsletter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return Newsletter
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set affiliateId
     *
     * @param integer $affiliateId
     *
     * @return Newsletter
     */
    public function setAffiliateId($affiliateId)
    {
        $this->affiliateId = $affiliateId;

        return $this;
    }

    /**
     * Get affiliateId
     *
     * @return integer
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }

    /**
     * Set types
     *
     * @param integer $types
     *
     * @return Newsletter
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return integer
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->types = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tracked_types = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add types
     *
     * @param \Zezda\AlertBundle\Entity\AlertType $types
     *
     * @return Newsletter
     */
    public function addType(\Zezda\AlertBundle\Entity\AlertType $types)
    {
        $this->types[] = $types;

        return $this;
    }

    /**
     * Remove types
     *
     * @param \Zezda\AlertBundle\Entity\AlertType $types
     */
    public function removeType(\Zezda\AlertBundle\Entity\AlertType $types)
    {
        $this->types->removeElement($types);
    }

    /**
     * Set guruName
     *
     * @param string $guruName
     *
     * @return Newsletter
     */
    public function setGuruName($guruName)
    {
        $this->guruName = $guruName;

        return $this;
    }

    /**
     * Get guruName
     *
     * @return string
     */
    public function getGuruName()
    {
        return $this->guruName;
    }

    /**
     * Add tracked_types
     *
     * @param \Zezda\AlertBundle\Entity\TrackedType $trackedTypes
     *
     * @return Newsletter
     */
    public function addTrackedType(\Zezda\AlertBundle\Entity\TrackedType $trackedTypes)
    {
        $this->tracked_types[] = $trackedTypes;

        return $this;
    }

    /**
     * Remove tracked_types
     *
     * @param \Zezda\AlertBundle\Entity\TrackedType $trackedTypes
     */
    public function removeTrackedType(\Zezda\AlertBundle\Entity\TrackedType $trackedTypes)
    {
        $this->tracked_types->removeElement($trackedTypes);
    }

    /**
     * Get tracked_types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrackedTypes()
    {
        return $this->tracked_types;
    }

    /**
     * Set isProfitly
     *
     * @param boolean $isProfitly
     *
     * @return Newsletter
     */
    public function setIsProfitly($isProfitly)
    {
        $this->isProfitly = $isProfitly;

        return $this;
    }

    /**
     * Get isProfitly
     *
     * @return boolean
     */
    public function getIsProfitly()
    {
        return $this->isProfitly;
    }

    /**
     * Set smsPrefix
     *
     * @param string $smsPrefix
     *
     * @return Newsletter
     */
    public function setSmsPrefix($smsPrefix)
    {
        $this->smsPrefix = $smsPrefix;

        return $this;
    }

    /**
     * Get smsPrefix
     *
     * @return string
     */
    public function getSmsPrefix()
    {
        return $this->smsPrefix;
    }

    /**
     * Add watchlists
     *
     * @param \Zezda\AlertBundle\Entity\Watchlist $watchlists
     * @return Newsletter
     */
    public function addWatchlist(\Zezda\AlertBundle\Entity\Watchlist $watchlists)
    {
        $this->watchlists[] = $watchlists;

        return $this;
    }

    /**
     * Remove watchlists
     *
     * @param \Zezda\AlertBundle\Entity\Watchlist $watchlists
     */
    public function removeWatchlist(\Zezda\AlertBundle\Entity\Watchlist $watchlists)
    {
        $this->watchlists->removeElement($watchlists);
    }

    /**
     * Get watchlists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWatchlists()
    {
        return $this->watchlists;
    }
}
