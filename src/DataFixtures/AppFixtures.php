<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Adoptant;
use App\Entity\Annonce;
use App\Entity\Annonceur;
use App\Entity\Dog;
use App\Entity\Image;
use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // fixture Admin

        $admin = new Admin();
        $admin
            ->setUsername('admin')
            ->setEmail('admin@gmail.com')
            ->setCity('lyon')
            ->setPhone('0123456789')
            ->setZipCode('69');
        $admin->setPassword(
            $this->hasher->hashPassword(
                $admin,
                '007'
            )
        );
        $manager->persist($admin);

        // Fixtures Adoptant
        $usersData = json_decode(file_get_contents("https://jsonplaceholder.typicode.com/users"), true);
        $adoptant = [];

        for ($i = 0; $i < 5; ++$i) {
            $userData = $usersData[$i];
            $adoptant = new Adoptant();

            $fullname = explode(' ', $userData['name']);
            $firstName = $fullname[0];
            $lastName = $fullname[1];
            $adoptant
                ->setUsername($userData["username"])
                ->setEmail($userData["email"])
                ->setCity($userData["address"]["city"])
                ->setPhone($userData["phone"])
                ->setZipCode($userData["address"]["zipcode"]);
            $adoptant->setPassword(
                $this->hasher->hashPassword(
                    $adoptant,
                    'mdp'
                )
            );
            $manager->persist($adoptant);
            $adoptants[] = $adoptant;
        }
        // fixture annonceur

        $annonceurs = [];

        $assocName = ['SecondeChance', 'RonRhone', 'PAAW', 'PetAdoption'];
        $assocData =
            [
                ['assocName' => 'SPA', 'email' => 'SPA@gmail.com'],
                ['assocName' => 'secondeChance', 'email' => 'secondeChance@gmail.com'],
                ['assocName' => 'ronRhone', 'email' => 'ronRhone@gmail.com'],
                ['assocName' => 'PAAW', 'email' => 'PAAW@gmail.com'],
                ['assocName' => 'PetAdoption', 'email' => 'PetAdoption@gmail.com'],
            ];
        for ($i = 5; $i < 10; ++$i) {
            $userData = $usersData[$i];
            $annonceur = new Annonceur();

            $annonceur
                ->setUsername($userData['username'])
                ->setEmail($assocData[$i - 5]['email'])
                ->setCity($userData['address']['city'])
                ->setPhone($userData['phone'])
                ->setZipCode($userData['address']['zipcode']);
            $annonceur
                ->setName($assocData[$i - 5]['assocName']);

            $annonceur->setPassword(
                $this->hasher->hashPassword(
                    $annonceur,
                    'mdp'
                )
            );
            $manager->persist($annonceur);
            $annonceurs[] = $annonceur;
        }

        // Fixture Annonce
        $annonces = [];
        for ($i = 1; $i < 15; ++$i) {
            $titleData = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/posts/'.$i), true);
            $title = $titleData['title'];
            $randAnnonceur = mt_rand(0, count($annonceurs) - 1);
            $annonce = new Annonce();
            $date = new \DateTimeImmutable();
            $annonce
                ->setIsAvailable(true)
                ->setTitle($title)
                ->setAnnonceur($annonceurs[$randAnnonceur])
                ->setCreatedAt($date)
                ->setModifiedAt($date);
            $manager->persist($annonce);
            $annonces[] = $annonce;
        }

        // Fixture Breed + Dog
        $dogsNames =
            [
                'Admiral',
                'Alfred',
                'Asher',
                'Barrett',
                'Bond',
                'Buddy',
                'Churchill',
                'Cyrano',
                'Dilbert',
                'Duke',
                'Farley',
                'Frito',
                'Hawk',
                'Hunter',
                'Jupiter',
                'Kingston',
                'Lex',
                'Meatball',
                'Nolan',
                'Pedro',
                'Redmond',
                'Rosco',
                'Rufus',
                'Spud',
                'Twinkie',
                'Whiskey',
            ];

        $breedDataAll = json_decode(file_get_contents('https://dogapi.dog/api/v2/breeds'), true);
        $breedData = $breedDataAll['data'];
        $races = [];
        for ($i = 0; $i < 10; ++$i) {
            $name = $breedData[$i]['attributes']['name'];
            $description = $breedData[$i]['attributes']['description'];
            $race = new Race();
            $race
                ->setName($name)
                ->setDescritpion($description);
            $races[] = $race;
            $manager->persist($race);
        }
        $dogs = [];
        $descrip = 'Chien adorable qui ne demande qu\'à être adopté';
        $antec = 'N\'a jamais mordu, mais est diabétique (comme tous les chiens du site)';

        foreach ($dogsNames as $dogName) {
            $dog = new Dog();
            $rnd = mt_rand(0, 1);
            $isAdopted = [true, false];

            $dog
                ->setName($dogName)
                ->setDescription($descrip)
                ->setAntecedant($antec)
                ->setAcceptAnimmals($isAdopted[$rnd])
                ->setIsAdopted(false)
                ->setIsLof(false);

            $randnb = mt_rand(1, 3);
            for ($i = 0; $i <= $randnb; ++$i) {
                $randomNumber = mt_rand(0, count($races) - 1);
                $dog->addRace($races[$randomNumber]);
            }
            $randAnnonce = mt_rand(0, count($annonces) - 1);
            $dog->setAnnonce($annonces[$randAnnonce]);
            $manager->persist($dog);
            $dogs[] = $dog;
        }

        // Fixtures Images
        $dogsImages =
            [
                'img/dogs/01.jpg',
                'img/dogs/02.jpg',
                'img/dogs/03.jpg',
                'img/dogs/04.jpg',
                'img/dogs/05.jpg',
                'img/dogs/06.jpg',
                'img/dogs/07.jpg',
                'img/dogs/08.jpg',
                'img/dogs/09.jpg',
                'img/dogs/01.jpg',
                'img/dogs/02.jpg',
                'img/dogs/03.jpg',
                'img/dogs/04.jpg',
                'img/dogs/05.jpg',
                'img/dogs/06.jpg',
                'img/dogs/07.jpg',
                'img/dogs/08.jpg',
                'img/dogs/09.jpg',
                'img/dogs/01.jpg',
                'img/dogs/02.jpg',
                'img/dogs/03.jpg',
                'img/dogs/04.jpg',
                'img/dogs/05.jpg',
                'img/dogs/06.jpg',
                'img/dogs/07.jpg',
                'img/dogs/08.jpg',
                'img/dogs/09.jpg',
            ];

        foreach ($dogsImages as $imagePath) {
            $randomNumber = mt_rand(0, count($dogs) - 1);
            $dog = $dogs[$randomNumber];
            $image = new Image();
            $image->setPath($imagePath);
            $image->setAlt('image de chien');
            $image->setDog($dog);
            $dog->addImage($image);

            $manager->persist($image);
        }
        $manager->flush();
    }
}
