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

class HomeController extends AbstractController
{
    /**
     * @Route("/{force_step}", name="home")
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function book(Request $request, Registry $workflows, $force_step = 0)
    {
        $test = $this->getMaxSeguence(10);
        echo $test;

        $session = $request->getSession();
        if ($session === null) {
            $session = new Session();
            $session->start();
        }

        $entityManager = $this->getDoctrine()->getManager();

        /** @var Sequence $sequence */
        $sequence = $session->get('sequence', new Sequence());
        if ($sequence === null) {
            $sequence = new Sequence();
        }
        if (\count($sequence->getParticipants()) === 0) {
            $sequence->addParticipant(new Participant());
        }
        $form = null;

        $forms = [
            'step1' => ['class' => ParticipantsType::class, 'data' => $sequence, 'options' => ['entityManager' => $entityManager]]
        ];

        $sequenceContainer = new SequenceContainer($sequence,$forms);
        $workflow = $workflows->get($sequenceContainer);

        // Update the currentState on the sequence
        $availablePlaces = ['step1'];
        try {
            $workflow->apply($sequenceContainer, 'to_confirmed');
            $availablePlaces[] = 'confirmed';
        } catch (LogicException $e) {
        }

        // set step if available
        if ($force_step !== 0 && \in_array($force_step,$availablePlaces)) {
            //dump('force step: '.$force_step);
            $sequenceContainer->currentPlace = $force_step;
        }

        $formDef = isset($forms[$sequenceContainer->currentPlace]) ? $forms[$sequenceContainer->currentPlace] : null;

        if ($formDef) {

            $form = $this->createForm($formDef['class'],$formDef['data'],$formDef['options']);

            // request verarbeiten
            //dump('handle request');
            $form->handleRequest($request);
            //dump('/ handle request');

            if ($form->isSubmitted()) {
                //dump("handled request");
                if ($form->getData() instanceof Sequence) {
                    $sequence = $form->getData();
                    /** @var Participant $participant */
                    foreach ($sequence->getParticipants() as $participant) {
                        $participant->setSequence($sequence);
                    }
                    $sequenceContainer->sequence = $sequence;
                }

            }

            if ($form->isSubmitted() && $form->isValid()) {

                if ($sequenceContainer->currentPlace === 'confirmed') {
                    $session->set('sequence', null);
                    return $this->redirectToRoute('book_completed');
                }
                // goto latest available step
                return $this->redirectToRoute('home');
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
        ]);
    }


    /**
     * @Route("/buchung-abgeschlossen", name="book_completed")
     */
    public function completed()
    {
        return $this->render('sequence/completed.html.twig', [
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
        printf("%ld\n",$max);
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
