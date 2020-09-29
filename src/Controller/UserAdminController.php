<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as BaseAdminController;

class UserAdminController extends BaseAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param User $entityInstance
     */
    public function persistEntity($entityManager, $entityInstance): void
    {
        $this->encodeUserPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param User $entityInstance
     */
    public function updateEntity($entityManager, $entityInstance): void
    {
        $this->encodeUserPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * @param User $entity
     */
    private function encodeUserPassword($entity): void
    {
        $entity->setPassword(
            $this->passwordEncoder->encodePassword(
                $entity,
                $entity->getPassword()
            )
        );
    }
}