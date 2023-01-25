<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Products\Category\Type\Event\CategoryEventConverter;
use BaksDev\Products\Category\Type\Event\CategoryEventType;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Products\Category\Type\Id\CategoryUidConverter;
use BaksDev\Products\Category\Type\Id\CategoryUidType;
use BaksDev\Products\Category\Type\Landing\Id\LandingUid;
use BaksDev\Products\Category\Type\Landing\Id\LandingUidType;
use BaksDev\Products\Category\Type\Offers\Id\OffersUid;
use BaksDev\Products\Category\Type\Offers\Id\OffersUidType;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUidType;
use BaksDev\Products\Category\Type\Section\Field\Id\FieldUid;
use BaksDev\Products\Category\Type\Section\Field\Id\FieldUidType;
use BaksDev\Products\Category\Type\Section\Id\SectionUid;
use BaksDev\Products\Category\Type\Section\Id\SectionUidType;
use BaksDev\Products\Category\Type\Settings\CategorySettings;
use BaksDev\Products\Category\Type\Settings\CategorySettingsType;

use Symfony\Config\DoctrineConfig;

return static function (ContainerConfigurator $container, DoctrineConfig $doctrine)
{
    

    $doctrine->dbal()->type(CategorySettings::TYPE)->class(CategorySettingsType::class);
    
    $doctrine->dbal()->type(FieldUid::TYPE)->class(FieldUidType::class);
    
    $doctrine->dbal()->type(OffersUid::TYPE)->class(OffersUidType::class);
    
    $doctrine->dbal()->type(LandingUid::TYPE)->class(LandingUidType::class);
    
    $doctrine->dbal()->type(CategoryUid::TYPE)->class(CategoryUidType::class);
	$container->services()->set(CategoryUid::class)
		->tag('controller.argument_value_resolver', ['priority' => 100])
	;
	
    
    $doctrine->dbal()->type(SectionUid::TYPE)->class(SectionUidType::class);
    
    $doctrine->dbal()->type(ParentCategoryUid::TYPE)->class(ParentCategoryUidType::class);
    
    /** CategoryEvent  */
    $doctrine->dbal()->type(CategoryEvent::TYPE)->class(CategoryEventType::class);
	$container->services()->set(CategoryEvent::class)
		->tag('controller.argument_value_resolver', ['priority' => 100])
	;
	
    
//    /** CategoryUid */
//    $doctrine->dbal()->type(CategoryUid::TYPE)->class(CategoryUidType::class,);
//    $container->services()->set(CategoryUidConverter::CONVERTER)
//      ->class(CategoryUidConverter::class)
//      ->tag('request.param_converter', ['converter' => CategoryUidConverter::CONVERTER]);
//
//    /** CategoryEvent */
//    $doctrine->dbal()->type(CategoryEvent::TYPE)->class(CategoryEventType::class,);
//
//    $container->services()->set(CategoryEventConverter::CONVERTER)
//      ->class(CategoryEventConverter::class)
//      ->tag('request.param_converter', ['converter' => CategoryEventConverter::CONVERTER]);
//
//
//    /** ParentCategoryUid */
//    $doctrine->dbal()->type(ParentCategoryUid::TYPE)->class(ParentCategoryUidType::class,);
//
//    $container->services()->set(ParentCategoryUidConverter::CONVERTER)
//      ->class(ParentCategoryUidConverter::class)
//      ->tag('request.param_converter', ['converter' => ParentCategoryUidConverter::CONVERTER]);
//
//
//    /** SectionUid */
//    $doctrine->dbal()->type(SectionUid::TYPE)->class(SectionUidType::class,);
//
//    $container->services()->set(SectionUidConverter::CONVERTER)
//      ->class(SectionUidConverter::class)
//      ->tag('request.param_converter', ['converter' => SectionUidConverter::CONVERTER]);
    
    
    $emDefault = $doctrine->orm()->entityManager('default');
    
    $emDefault->autoMapping(true);
    $emDefault->mapping('ProductCategory')
      ->type('attribute')
      ->dir('%kernel.project_dir%/src/Module/Products/Category/Entity')
      ->isBundle(false)
      ->prefix('BaksDev\Products\Category\Entity')
      ->alias('ProductCategory')
    ;
};