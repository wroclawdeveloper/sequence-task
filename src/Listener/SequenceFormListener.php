<?php
namespace App\Listener;

use App\Entity\SequenceContainer;
use App\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Workflow\Event\GuardEvent;

class SequenceFormListener implements EventSubscriberInterface
{

    public function validateForm(GuardEvent $event)
    {
        /** @var SequenceContainer $sequenceContainer */
        $sequenceContainer = $event->getSubject();

        dump("validateForm for ".$sequenceContainer->currentPlace);

        $forms = $sequenceContainer->forms;

        $formDef = $forms[$sequenceContainer->currentPlace];

        $validator = Validation::createValidator();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();

        //dump($formDef['data']);
        $form = $formFactory->create($formDef['class'],$formDef['data'],$formDef['options']);

        dump("fake submit for form ".$form->getName());
        //$form->submit([],false);  // Leider nicht möglich
        $this->validateWithoutSubmit($form);


        $errors = $form->getErrors(true);
        // Validator auf Fehler abfragen
        if (\count($errors) > 0) {
            dump($sequenceContainer->currentPlace." blocked");
            //dump($errors);
            $event->setBlocked(true);
        } else {
            dump($sequenceContainer->currentPlace." not blocked");
        }

        //$event->setBlocked(true);

    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.sequence.guard.to_confirmed' => ['validateForm'],
            'workflow.sequence.guard.to_step3' => ['validateForm'],
            'workflow.sequence.guard.to_step2' => ['validateForm'],
        ];
    }


    private function validateWithoutSubmit(FormInterface $form)
    {
        $config = $form->getConfig();
        $children = $form->all();
        $dispatcher = $config->getEventDispatcher();
        $viewData = $form->getData();

        if ($config->getCompound()) {

            /**
             * @var string $name
             * @var FormInterface $child
             */
            foreach ($children as $name => $child) {
                $this->validateWithoutSubmit($child);
            }
        }

        if ($dispatcher->hasListeners(FormEvents::POST_SUBMIT)) {
            $event = new FormEvent($form, $viewData);
            $dispatcher->dispatch(FormEvents::POST_SUBMIT, $event);
        }

    }

}