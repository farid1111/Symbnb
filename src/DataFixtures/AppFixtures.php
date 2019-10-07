<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder= $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        
        $adminUser = new User();
        $adminUser->setFirstName('Farid')
                 ->setLastName('Boussetta')
                 ->setEmail('farid@mail.com')
                 ->setHash($this->encoder->encodePassword($adminUser,'password'))
                 ->setPicture('http://avatars.io/twitter/LiiorC')
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                 ->addUserRole($adminRole);
        $manager->persist($adminUser);
        
        $users=[];
        $genres=['male','female'];
        //nous gerons les users
        for ($i=1; $i < 10; $i++) { 
            $user= new User();
            $genre=$faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99).'.jpg';
            $picture.=($genre=='male' ? 'men/': 'women/').$pictureId;
            $hash=$this->encoder->encodePassword($user,'password');
            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence)
                 ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users[]=$user;
        }
        
        //nous gerons les annonces
        for ($i=1; $i < 30; $i++) { 
            
            $ad= new Ad();

            $title= $faker->sentence();
            $coverImage=$faker->imageUrl(1000,350);
            $introduction= $faker->paragraph(2);
            $content='<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            $user=$users[mt_rand(0,count($users)-1)];
            $ad->setTitle($title)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(40,200))
            ->setRooms(mt_rand(2,6))
            ->setAuthor($user);
            for ($j=1; $j < mt_rand(2,6); $j++){
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);

        }
        $manager->flush();
    }
}
