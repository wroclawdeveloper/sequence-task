<?php

namespace App\Form\Extension\Validator\ViolationMapper;

use Symfony\Component\Form\Extension\Validator\ViolationMapper\MappingRule;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\RelativePath;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapperInterface;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationPath;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationPathIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\InheritDataAwareIterator;
use Symfony\Component\PropertyAccess\PropertyPathIterator;
use Symfony\Component\PropertyAccess\PropertyPathBuilder;
use Symfony\Component\PropertyAccess\PropertyPathIteratorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\ConstraintViolation;

class ViolationMapper implements ViolationMapperInterface
{
    /**
     * @var bool
     */
    private $allowNonSynchronized;

    /**
     * {@inheritdoc}
     */
    public function mapViolation(ConstraintViolation $violation, FormInterface $form, $allowNonSynchronized = false)
    {
    }

    /**
     * @return bool
     */
    private function acceptsErrors(FormInterface $form)
    {
        return true;
    }
}
