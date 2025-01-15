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
    private FilmRepository $filmRepository;
    private entityMapper $entityMapper;

    public function __construct()
    {
        $this -> filmRepository = new FilmRepository();
        $this->renderer = new TemplateRenderer();
        $this -> entityMapper = new EntityMapper();
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

            //dans le constructeur
            $film = $this -> entityMapper->mapToEntity($data, Film::class);

            //dans le constructeur
            $this -> filmRepository->save($film);

            header('Location: /film/list');
        } else {
            echo $this->renderer->render('film/create.html.twig');
        }
    }

    // src/Controller/FilmController.php

    // src/Controller/FilmController.php

    public function read(array $queryParams): void
    {
        $filmId = (int) $queryParams['id'];

        $film = $this -> filmRepository->find($filmId);

        if (!$film) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Film non trouvé']);
            return;
        }

        $html = $this->renderer->render('film/read.html.twig', [
            'film' => $film,
        ]);

        header('Content-Type: text/html');
        echo $html;
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

            $film = $this -> entityMapper->mapToEntity($data, Film::class);

            $this -> filmRepository->update($film);

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

            $film = $this -> filmRepository->find($filmId);

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
            $filmId = (int) $_POST['id'];

            $this -> filmRepository->delete($filmId);

            header('Location: /film/list');
            exit;
        }
    }
}
