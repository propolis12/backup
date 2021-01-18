<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy random()
 * @method static User[]|Proxy[] randomSet(int $number)
 * @method static User[]|Proxy[] randomRange(int $min, int $max)
 * @method static UserRepository|RepositoryProxy repository()
 * @method User|Proxy create($attributes = [])
 * @method User[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function getDefaults(): array
    {
        return [
            'firstname' => self::faker()->firstName(),
            'lastname' => explode(' ',trim(self::faker()->name()))[1],
            //'username' => self::faker()->firstname(),
            'email' => self::faker()->email,
            'roles' => self::faker()->boolean(80) ? ['ROLE_USER'] : ['ROLE_ADMIN'],
            'registeredAt' => self::faker()->dateTimeBetween('-20 years', 'now'),
            'agreedTermsAt' => new \DateTime(),
            'password' => $this->userPasswordEncoder->encodePassword(new User(),'cvina')
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            ->afterInstantiate(function(User $user) {
                $user->setUsername($user->getFirstname());
            })
            ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
