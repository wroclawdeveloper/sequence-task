<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sequence",inversedBy="participants")
     * @ORM\JoinColumn(name="sequence_id", referencedColumnName="id")
     **/
    private $sequence;


    /**
     * @var string
     * @ORM\Column(name="input_number", type="integer")
     */
    private $inputNumber = '';

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param mixed $sequence
     */
    public function setSequence($sequence): void
    {
        $this->sequence = $sequence;
    }

    /**
     * @return integer
     */
    public function getinputNumber(): string
    {
        return $this->inputNumber;
    }

    /**
     * @param integer $inputNumber
     */
    public function setinputNumber(string $inputNumber): void
    {
        $this->inputNumber = $inputNumber;
    }
}
