<?php
namespace App\Controller;
use App\Entity\Film;

class FilmValidator {

    public function validateFilm(Film $film) : bool {
        return true;
    }
}