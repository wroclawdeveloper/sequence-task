<?php
/**
 * Created by PhpStorm.
 * User: svewap
 * Date: 23.03.18
 * Time: 11:05
 */

namespace App\Entity;


class SequenceContainer
{

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


    /* workflow marking store */
    public $currentPlace;

}