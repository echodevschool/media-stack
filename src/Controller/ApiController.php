<?php

namespace App\Controller;

use App\Entity\Music;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/api/music', name: 'music.all')]
    public function musicAll()
    {
        $entityManager = $this->managerRegistry->getManager();
        $musics = $entityManager->getRepository(Music::class)->findAll();
        $responseData = [];
        foreach ($musics as $music) {
            $responseData[] = [
                'id' => $music->getId(),
                'name' => $music->getName(),
                'artist' => $music->getArtist()
            ];
        }

        return new JsonResponse($responseData, 200);
    }
}