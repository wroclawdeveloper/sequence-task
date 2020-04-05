<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sequence;
use App\Entity\SequenceContainer;
use App\Form\Sequence\ParticipantsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @var array
     */
    public $max = null;

    /**
     * @Route("/{force_step}", name="home")
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sequence(Request $request, Registry $workflows, $force_step = 0)
    {
        $session = $request->getSession();
        if ($session === null) {
            $session = new Session();
            $session->start();
        }
        $sequence = $session->get('sequence', new Sequence());
        if ($request->get('force_step') === 'new') {
            $sequence = $session->set('sequence', null);
            return $this->redirectToRoute('home', array('force_step' => 'step1'));
        }

        $entityManager = $this->getDoctrine()->getManager();

        /** @var Sequence $sequence */
        $sequence = $session->get('sequence', new Sequence());
        if ($sequence === null) {
            $sequence = new Sequence();
        }
        foreach ($sequence->getParticipants() as $participant) {
            if (empty($participant->getinputNumber())) {
                $sequence->removeParticipant($participant);
            }
        }
        if (count($sequence->getParticipants()) < 10) {
            $sequence->addParticipant(new Participant());
        }
        $form = null;

        $forms = [
            'step1' => ['class' => ParticipantsType::class, 'data' => $sequence, 'options' => ['entityManager' => $entityManager]],
            'confirmed' => ['class' => ParticipantsType::class, 'data' => $sequence, 'options' => ['entityManager' => $entityManager]]
        ];

        $sequenceContainer = new SequenceContainer($sequence, $forms);
        $workflow = $workflows->get($sequenceContainer);

        // Update the currentState on the sequence
        $availablePlaces = ['step1'];
        try {
            $workflow->apply($sequenceContainer, 'to_confirmed');
            $availablePlaces[] = 'confirmed';
        } catch (LogicException $e) {
        }

        // set step if available
        if ($force_step !== 0 && in_array($force_step,$availablePlaces)) {
            $sequenceContainer->currentPlace = $force_step;
        }

        $formDef = isset($forms[$sequenceContainer->currentPlace]) ? $forms[$sequenceContainer->currentPlace] : null;

        if ($formDef) {

            $form = $this->createForm($formDef['class'],$formDef['data'],$formDef['options']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->getData() instanceof Sequence) {
                    $sequence = $form->getData();
                    $this->result = [];
                    /** @var Participant $participant */
                    foreach ($sequence->getParticipants() as $participant) {
                        $participant->setSequence($sequence);
                        $number = (int)$participant->getinputNumber();
                        $this->getMaxSeguence($number);
                        $participant->setResult($this->max);
                    }
                    $sequenceContainer->sequence = $sequence;
                }
                return $this->redirectToRoute('home', array('force_step' => 'step1'));
            }
        }

        // See all the available transitions for the post in the current state
        $transitions = $workflow->getEnabledTransitions($sequenceContainer);
        $session->set('sequence', $sequence);

        return $this->render('sequence/sequence.html.twig', [
            'sequence' => $sequence,
            'transactions' => $transitions,
            'form' => $form ? $form->createView() : null,
            'sequenceContainer' => $sequenceContainer,
            'availablePlaces' => $availablePlaces,
            'numParticipants' => ($sequence !== null) ? count($sequence->getParticipants()) : 0,
            'result' => $this->max
        ]);
    }

    public function getMaxSeguence($n)
    {
        for($i = 1; $i <= $n; $i++)
        {
            $number[$i] = $this->getSeguence($i);
        }

        if ($n==0) return 0;
        for($i=0, $max=0; $i<=$n; $i++) {
            if (($sec = $this->getSeguence($i)) > $max) {
                $max = $sec;
            }
        }
        $this->max = $max;
//        printf("%ld\n",$max);
    }

    public function getSeguence($n)
    {
        if ($n == 0)
            return 0;
        else if ($n == 1)
            return 1;
        else if( $n % 2 == 0 )
            return $this->getSeguence($n/2);
        else
            return ($this->getSeguence(($n-1)/2) + $this->getSeguence(($n-1)/2 + 1));
    }
}
