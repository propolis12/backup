<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        //
        // $product = new Product();
        //$user = new User();
        //$user->setPassword($this->userPasswordEncoder->encodePassword($user, 'cvina'));
        $user = UserFactory::new()->create();
        //$manager->persist($user);
        // $manager->flush();

        //UserFactory::new()->createMany(20);

    }
}
