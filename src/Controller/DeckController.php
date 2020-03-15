<?php


namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Type;
use App\Form\DeckType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeckController extends AbstractController
{
    /**
     * @Route("/newDeck", name="newDeck")
     */
    public function createDeck(Request $request)
    {
        $type = new Deck();
        $form = $this->createForm(DeckType::class, $type);

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
     * @Route("/decks", name="decks")
     */
    public function getDecks()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $decks = $entityManager->getRepository(Deck::class)->findAll();

        return $this->render('home/listDecks.html.twig', [
            'decks' => $decks,
        ]);
    }

    /**
     * @Route("/editDeck/{deckId}", name="editDeck")
     */
    public function editDeck(Request $request, $deckId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $deck = $entityManager->getRepository(Deck::class)->find($deckId);
        $decklist = $deck->getCards();
        $deckName = $deck->getName();
        $cards = $entityManager->getRepository(Card::class)->findAll();

        return $this->render('home/deck.html.twig', [
            'deckId' => $deckId,
            'name' => $deckName,
            'list' => $decklist,
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/newDeck", name="newDeck")
     */
    public function newDeck()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $cards = $entityManager->getRepository(Card::class)->findAll();

        return $this->render('home/deck.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/saveDeck", name="saveDeck")
     */
    public function saveDeck(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $deckListId = $request->request->get("deckList");
        $deckName = $request->request->get("deckName");
        $deckId = $request->request->get("deckId");
        $repo = $entityManager->getRepository(Deck::class);

        if ($deckId === 0) {
            $deck = new Deck();
        } else {
            $deck = $repo->find($deckId);
            dump($deckId);
        }


        $deck->setName($deckName);

        foreach ($deckListId as $cardId) {
            $card = $entityManager->getRepository(Card::class)->find($cardId);
            $deck->addCard($card);
        }

        $entityManager->persist($deck);
        $entityManager->flush();

        $decks = $entityManager->getRepository(Deck::class)->findAll();

        return $this->render('home/listDecks.html.twig', [
            'decks' => $decks,
        ]);
    }

}