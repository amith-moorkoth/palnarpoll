<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * vote
 *
 * @ORM\Table(name="vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\voteRepository")
 */
class vote
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
     * @var int
     *
     * @ORM\Column(name="poll_id", type="integer")
     */
    private $poll_id;

    /**
     * @var string
     *
     * @ORM\Column(name="email_id", type="text")
     */
    private $email_id;

    /**
     * @var string
     *
     * @ORM\Column(name="vote", type="text")
     */
    private $vote;

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
     * Set poll_id
     *
     * @param integer $poll_id
     * @return vote
     */
    public function setPollId($poll_id)
    {
        $this->poll_id = $poll_id;

        return $this;
    }

    /**
     * Get poll_id
     *
     * @return integer 
     */
    public function getPollId()
    {
        return $this->poll_id;
    }

    /**
     * Set email_id
     *
     * @param string $email_id
     * @return vote
     */
    public function setEmailId($email_id)
    {
        $this->email_id = $email_id;

        return $this;
    }

    /**
     * Get email_id
     *
     * @return string 
     */
    public function getEmailId()
    {
        return $this->email_id;
    }

    /**
     * Set vote
     *
     * @param string $vote
     * @return vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return string 
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return vote
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
