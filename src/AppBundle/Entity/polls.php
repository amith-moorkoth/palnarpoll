<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * polls
 *
 * @ORM\Table(name="polls")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\pollsRepository")
 */
class polls
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="poll_ques", type="text")
     */
    private $pollQues;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text")
     */
    private $options;

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="text")
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * Set pollQues
     *
     * @param string $pollQues
     * @return polls
     */
    public function setPollQues($pollQues)
    {
        $this->pollQues = $pollQues;

        return $this;
    }

    /**
     * Get pollQues
     *
     * @return string 
     */
    public function getPollQues()
    {
        return $this->pollQues;
    }

    /**
     * Set options
     *
     * @param string $options
     * @return polls
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return string 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set duration
     *
     * @param string $duration
     * @return polls
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return polls
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
