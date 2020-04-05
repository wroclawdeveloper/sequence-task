<?php

namespace App\Entity;

class SequenceContainer
{
    /**
     * SequenceContainer constructor.
     * @param $sequence
     * @param $forms
     */
    public function __construct($sequence, $forms)
    {
        $this->sequence = $sequence;
        $this->forms = $forms;
    }

    /**
     * @var Sequence
     */
    public $sequence;

    /**
     * @var array
     */
    public $forms;

    /**
     * @var
     */
    public $currentPlace;

}