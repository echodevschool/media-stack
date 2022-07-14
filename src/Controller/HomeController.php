<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\MusicFormType;
use App\Repository\MusicRepository;
use App\Entity\MusicGenre;
use App\Form\MusicGenreFormType;
use App\Repository\MusicGenreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    public ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $entityManager = $this->managerRegistry->getManager();
        $musics = $entityManager->getRepository(Music::class)->findAll();

        $music = new Music();
        $form = $this->createForm(MusicFormType::class, $music);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('m_wallpeper')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('music_wallpaper'),
                        $newFilename
                    );
                } catch (FileException $exception) {
                    dd($exception->getMessage());
                }

                $music->setWallpaper($newFilename);
            }
            $entityManager->persist($music);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'musics' => $musics,
            'form' => $form->createView()
        ]);
    }

    #[Route('/genre', name: 'app_genre')]
    public function musicGenre(Request $request): Response
    {
        $entityManager = $this->managerRegistry->getManager();
        $musicGenre = $entityManager->getRepository(MusicGenre::class)->findAll();

        $genre = new MusicGenre();
        $form = $this->createForm(MusicGenreFormType::class, $genre);
        $form->handleRequest($request);
        
        return $this->render('genre/', [
            'controller_name' => 'HomeController',
            'genre' => $musicGenre,
            'form' => $form->createView()
        ]);
    }    
}
