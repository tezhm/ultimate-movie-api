<?php declare(strict_types=1);
namespace Uma\Database\Seeds;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Database\Seeder;
use Nelmio\Alice\Fixtures\Loader;
use Uma\Domain\Model\Actor;
use Uma\Domain\Model\Genre;
use Uma\Domain\Model\Movie;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the Database Seeds.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function run(EntityManagerInterface $entityManager)
    {
        $loader = new Loader('en_US');
        $actors = $loader->load(__DIR__ . '/Fixtures/Actors.yml');
        $movies = $loader->load(__DIR__ . '/Fixtures/Movies.yml');
        $genres = $loader->load(__DIR__ . '/Fixtures/Genres.yml');
        $users = $loader->load(__DIR__ . '/Fixtures/User.yml');

        $this->updateActors($actors);
        $this->updateGenres($genres, $movies, $actors);
        $this->updateMovies($movies, $actors, $genres);

        $this->persist($entityManager, array_merge($actors, $movies, $genres, $users));
    }

    /**
     * Persist all the given entities.
     *
     * @param EntityManagerInterface $entityManager
     * @param array $entities
     */
    private function persist(EntityManagerInterface $entityManager, array $entities)
    {
        $entityManager->transactional(function () use ($entityManager, $entities)
        {
            foreach ($entities as $entity)
            {
                $entityManager->persist($entity);
            }
        });
    }

    /**
     * Updates the images of the actors.
     *
     * @param Actor[] $actors
     */
    private function updateActors(array $actors)
    {
        $mike = file_get_contents(__DIR__ . '/Fixtures/mike_myers.jpg');
        $actors['Actor1']->setImage(base64_encode($mike));

        $heather = file_get_contents(__DIR__ . '/Fixtures/heather_graham.jpg');
        $actors['Actor2']->setImage(base64_encode($heather));

        $shia = file_get_contents(__DIR__ . '/Fixtures/shia_labeouf.jpg');
        $actors['Actor3']->setImage(base64_encode($shia));

        $megan = file_get_contents(__DIR__ . '/Fixtures/megan_fox.jpg');
        $actors['Actor4']->setImage(base64_encode($megan));

        $mark = file_get_contents(__DIR__ . '/Fixtures/mark_ryan.jpg');
        $actors['Actor5']->setImage(base64_encode($mark));
    }

    /**
     * Adds the movies and actors to genres.
     *
     * @param Genre[] $genres
     * @param Movie[] $movies
     * @param Actor[] $actors
     */
    private function updateGenres(array $genres, array $movies, array $actors)
    {
        $genres['Genre1']->addMovie($movies['Movie1']);
        $genres['Genre1']->addMovie($movies['Movie2']);

        $genres['Genre2']->addMovie($movies['Movie2']);
        $genres['Genre2']->addActor($actors['Actor1']);
    }

    /**
     * Adds the actors and genres to movies.
     *
     * @param Movie[] $movies
     * @param Actor[] $actors
     * @param Genre[] $genres
     */
    private function updateMovies(array $movies, array $actors, array $genres)
    {
        $transformers = file_get_contents(__DIR__ . '/Fixtures/transformers.jpg');
        $movies['Movie1']->setImage(base64_encode($transformers));
        $movies['Movie1']->addActor('Sam Witwicky', $actors['Actor3']);
        $movies['Movie1']->addActor('Mikaela Banes', $actors['Actor4']);
        $movies['Movie1']->addActor('Bumblebee', $actors['Actor5']);
        $movies['Movie1']->setGenre($genres['Genre1']);

        $austinPowers = file_get_contents(__DIR__ . '/Fixtures/austin_powers.jpg');
        $movies['Movie2']->setImage(base64_encode($austinPowers));
        $movies['Movie2']->addActor('Austin Powers', $actors['Actor1']);
        $movies['Movie2']->addActor('Felicity Shagwell', $actors['Actor2']);
        $movies['Movie2']->setGenre($genres['Genre2']);
    }
}
