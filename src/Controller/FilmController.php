<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\TemplateRenderer;
use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Service\EntityMapper;

class FilmController
{
    private TemplateRenderer $renderer;

    public function __construct()
    {
        $this->renderer = new TemplateRenderer();
    }

    public function list(array $queryParams)
    {
        $filmRepository = new FilmRepository();
        $films = $filmRepository->findAll();

        /* $filmEntities = [];
        foreach ($films as $film) {
            $filmEntity = new Film();
            $filmEntity->setId($film['id']);
            $filmEntity->setTitle($film['title']);
            $filmEntity->setYear($film['year']);
            $filmEntity->setType($film['type']);
            $filmEntity->setSynopsis($film['synopsis']);
            $filmEntity->setDirector($film['director']);
            $filmEntity->setCreatedAt(new \DateTime($film['created_at']));
            $filmEntity->setUpdatedAt(new \DateTime($film['updated_at']));

            $filmEntities[] = $filmEntity;
        } */

        //dd($films);

        echo $this->renderer->render('film/list.html.twig', [
            'films' => $films,
        ]);

        // header('Content-Type: application/json');
        // echo json_encode($films);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? null,
                'year' => $_POST['year'] ?? null,
                'type' => $_POST['type'] ?? null,
                'synopsis' => $_POST['synopsis'] ?? null,
                'director' => $_POST['director'] ?? null,
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            $entityMapper = new EntityMapper();
            $film = $entityMapper->mapToEntity($data, Film::class);

            $filmRepository = new FilmRepository();
            $filmRepository->save($film);

            header('Location: /film/list');
        } else {
            echo $this->renderer->render('film/create.html.twig');
        }
    }

    public function read(array $queryParams)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filmId'])) {
            $filmId = $_POST['filmId'];
            $filmRepository = new FilmRepository();
            $film = $filmRepository->find($filmId);
            echo $this->renderer->render('film/edit.html.twig', [
                'film' => $film,
            ]);
        } else {
            echo $this->renderer->render('film/list.html.twig');
        }
        $filmRepository = new FilmRepository();
        $film = $filmRepository->find((int) $queryParams['id']);

        dd($film);
    }

    public function edit(array $queryParams)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => (int) $_POST['id'] ?? null,
                'title' => $_POST['title'] ?? null,
                'year' => $_POST['year'] ?? null,
                'type' => $_POST['type'] ?? null,
                'synopsis' => $_POST['synopsis'] ?? null,
                'director' => $_POST['director'] ?? null,
            ];

            $entityMapper = new EntityMapper();
            $film = $entityMapper->mapToEntity($data, Film::class);

            $filmRepository = new FilmRepository();
            $filmRepository->update($film);

            header('Location: /film/list');
            exit;
        } else {
            echo $this->renderer->render('film/edit.html.twig');
        }
    }

    public function searchByID(array $queryParams)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filmId'])) {
            $filmId = (int) $_POST['filmId'];

            $filmRepository = new FilmRepository();
            $film = $filmRepository->find($filmId);

            if ($film) {
                echo $this->renderer->render('film/edit.html.twig', [
                    'film' => $film,
                ]);
            } else {
                echo "Aucun film trouvé avec cet ID.";
            }
        } else {
            echo $this->renderer->render('film/edit.html.twig');
        }
    }
    public function delete(array $queryParams)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            // Récupérer l'ID du film à supprimer
            $filmId = (int) $_POST['id'];

            // Supprimer le film de la base de données
            $filmRepository = new FilmRepository();
            $filmRepository->delete($filmId);

            // Rediriger vers la liste des films
            header('Location: /film/list');
            exit;
        }
    }
}
