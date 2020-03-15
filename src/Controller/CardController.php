<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Type;
use App\Form\CardType;
use App\Form\TypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class CardController extends AbstractController
{
     /**
     * @Route("/card", name="card")
     */
    public function newCard(Request $request)
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                try {
                    $image->move(
                        $this->getParameter('images'),
                        $originalFilename
                    );
                } catch (FileException $e) {
                }

                $card->setImage($originalFilename);
            } else {
                $card->setImage(
                    new File($this->getParameter('images').'/'.$card->getBrochureFilename())
                );
            }

            $user = $this->getUser();
            $card = $form->getData();
            $card->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/type", name="type")
     */
    public function newType(Request $request)
    {
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $type = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cards", name="cards")
     */
    public function getCards()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $cards = $entityManager->getRepository(Card::class)->findAll();

        return $this->render('home/listCards.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/saveCardDeck", name="saveCardDeck", methods={"POST"})
     */
    public function getCardDeck(Request $request)
    {
        $idCard = $request->request->get("idCard");
        $entityManager = $this->getDoctrine()->getManager();
        $cards = $entityManager->getRepository(Card::class)->findAll();
        $card = $entityManager->getRepository(Card::class)->find($idCard);

        return $this->render('home/listCards.html.twig', [
            'card' => $card,
            'cards' => $cards,
        ]);
    }
}
