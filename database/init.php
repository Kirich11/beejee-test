<?php
/**
 * Basic hardcoded seeds
 */

require_once __DIR__."/../vendor/autoload.php";
use App\DbManager;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Symfony\Component\Dotenv\Dotenv;


$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$roleRepository = DbManager::getRepository(Role::class);
$roles = $roleRepository->find(1);
if (empty($roles)) {
    $manager = DbManager::createManager();
    $role = new Role();
    $role->setValue('admin');
    $manager->persist($role);    

    $statusNew = new Status();
    $statusNew->setValue('new');
    $manager->persist($statusNew);

    $statusDone = new Status();
    $statusDone->setValue('done');
    $manager->persist($statusDone);

    $user = new User();
    $user->setFirstname('admin');
    $user->setLastname('admin');
    $user->setEmail('admin@admin.admin');
    $user->setLogin('admin');
    $user->setPassword('123');
    $user->setRole($role);
    $manager->persist($user);
    
    $manager->flush();
}


