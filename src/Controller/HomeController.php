<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\MusicFormType;
use App\Repository\MusicRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $entityManager = $this->managerRegistry->getManager();
        $musics = $entityManager->getRepository(Music::class)->findAll();

        $music = new Music();
        $form = $this->createForm(MusicFormType::class, $music);
        //$request->query->get('key'); get
        //$request->request->get('key'); post
        //$request->get('key'); get -> post
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($music);
            $entityManager->flush();
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'musics' => $musics,
            'form' => $form->createView()
        ]);
    }
}
