<?php

namespace App\Form\Extension\Validator\Type;

use App\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\Type\BaseValidatorExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Validator\EventListener\ValidationListener;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sven Wappler
 */
class FormTypeValidatorExtension extends BaseValidatorExtension
{
    private $validator;
    private $violationMapper;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->violationMapper = new ViolationMapper();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ValidationListener($this->validator, $this->violationMapper));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // Constraint should always be converted to an array
        $constraintsNormalizer = function (Options $options, $constraints) {
            return is_object($constraints) ? array($constraints) : (array) $constraints;
        };

        $resolver->setDefaults(array(
            'error_mapping' => array(),
            'constraints' => array(),
            'invalid_message' => 'This value is not valid.',
            'invalid_message_parameters' => array(),
            'allow_extra_fields' => false,
            'extra_fields_message' => 'This form should not contain extra fields.',
        ));

        $resolver->setNormalizer('constraints', $constraintsNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FormType';
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
