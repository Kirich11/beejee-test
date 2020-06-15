<?php
/**
 * Basic hardcoded seeds
 */

require_once "vendor/autoload.php";
use App\DbManager;
use App\Models\Role;
use Symfony\Component\Dotenv\Dotenv;


$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$roleRepository = DbManager::getRepository(Role::class);
$roles = $roleRepository->find(1);
if (empty($roles)) {
    $sql = "insert roles(`value`) values ('admin');
            insert statuses(`value`) values('new');
            insert statuses(`value`) values('done');
            insert users(`firstname`, `lastname`, `email`, `login`, `role_id`, `password`) values('admin', 'admin', 'admin@admin.com', 'admin', 1, '$2y$10$6AwuvnwgHqMdHWelHN5chucw0OlWsD6Dub99jJXv8n95PMgpZiYo.');";

    $manager = DbManager::createManager();
    $statement = $manager->getConnection()->prepare($sql);
    $statement->execute();

    $statement->closeCursor();
}


