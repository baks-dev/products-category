<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

//use App\Module\Product\Type\Category\Id\CategoryUidConverter;
//use App\Module\User\Entity\User;
//use App\Module\Product\Entity;
//use App\Module\Product\EntityListeners;

return static function (ContainerConfigurator $configurator)
{
    $services = $configurator->services()
      ->defaults()
      ->autowire()      // Automatically injects dependencies in your services.
      ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    /** Services */
    
    $services->load('App\Module\Products\Category\Controller\\', '../../Controller')
      ->tag('controller.service_arguments');
    
    $services->load('App\Module\Products\Category\Repository\\', '../../Repository')
      ->exclude('../../Repository/**/*DTO.php');
      //->tag('controller.service_arguments');
    
    $services->load('App\Module\Products\Category\UseCase\\', '../../UseCase')
      ->exclude('../../UseCase/**/*DTO.php');
     // ->tag('controller.service_arguments');

    $services->load('App\Module\Products\Category\DataFixtures\\', '../../DataFixtures')
      ->exclude('../../DataFixtures/**/*DTO.php');
      //->tag('controller.service_arguments');
    

};

