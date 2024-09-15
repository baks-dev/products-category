<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Products\Category\BaksDevProductsCategoryBundle;
use BaksDev\Products\Category\Type\Event\CategoryProductEventType;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Products\Category\Type\Id\CategoryProductType;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\Type\Landing\Id\CategoryProductLandingType;
use BaksDev\Products\Category\Type\Landing\Id\CategoryProductLandingUid;
use BaksDev\Products\Category\Type\Offers\Id\CategoryProductOffersType;
use BaksDev\Products\Category\Type\Offers\Id\CategoryProductOffersUid;
use BaksDev\Products\Category\Type\Offers\Modification\CategoryProductModificationType;
use BaksDev\Products\Category\Type\Offers\Modification\CategoryProductModificationUid;
use BaksDev\Products\Category\Type\Offers\Type\CategoryProductModificationTypeType;
use BaksDev\Products\Category\Type\Offers\Type\CategoryProductModificationTypeUid;
use BaksDev\Products\Category\Type\Offers\Variation\CategoryProductVariationType;
use BaksDev\Products\Category\Type\Offers\Variation\CategoryProductVariationUid;
use BaksDev\Products\Category\Type\Parent\ParentCategoryProductType;
use BaksDev\Products\Category\Type\Parent\ParentCategoryProductUid;
use BaksDev\Products\Category\Type\Section\Field\Const\CategoryProductSectionFieldConst;
use BaksDev\Products\Category\Type\Section\Field\Const\CategoryProductSectionFieldConstType;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldType;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldUid;
use BaksDev\Products\Category\Type\Section\Id\CategoryProductSectionType;
use BaksDev\Products\Category\Type\Section\Id\CategoryProductSectionUid;
use BaksDev\Products\Category\Type\Settings\CategoryProductSettingsIdentifier;
use BaksDev\Products\Category\Type\Settings\CategoryProductSettingsType;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine) {

    $doctrine->dbal()->type(CategoryProductSettingsIdentifier::TYPE)->class(CategoryProductSettingsType::class);
    $doctrine->dbal()->type(CategoryProductSectionFieldUid::TYPE)->class(CategoryProductSectionFieldType::class);
    $doctrine->dbal()->type(CategoryProductSectionFieldConst::TYPE)->class(CategoryProductSectionFieldConstType::class);

    $doctrine->dbal()->type(CategoryProductOffersUid::TYPE)->class(CategoryProductOffersType::class);
    $container->services()->set(CategoryProductOffersUid::class)
        ->tag('controller.argument_value_resolver');


    $doctrine->dbal()->type(CategoryProductLandingUid::TYPE)->class(CategoryProductLandingType::class);
    $doctrine->dbal()->type(CategoryProductUid::TYPE)->class(CategoryProductType::class);
    $doctrine->dbal()->type(CategoryProductSectionUid::TYPE)->class(CategoryProductSectionType::class);
    $doctrine->dbal()->type(ParentCategoryProductUid::TYPE)->class(ParentCategoryProductType::class);
    $doctrine->dbal()->type(CategoryProductEventUid::TYPE)->class(CategoryProductEventType::class);
    $doctrine->dbal()->type(CategoryProductVariationUid::TYPE)->class(CategoryProductVariationType::class);
    $doctrine->dbal()->type(CategoryProductModificationUid::TYPE)->class(CategoryProductModificationType::class);
    $doctrine->dbal()->type(CategoryProductModificationTypeUid::TYPE)->class(CategoryProductModificationTypeType::class);


    /** Резолверы */
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CategoryProductUid::class)->class(CategoryProductUid::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);


    $emDefault->mapping('products-category')
        ->type('attribute')
        ->dir(BaksDevProductsCategoryBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevProductsCategoryBundle::NAMESPACE.'\\Entity')
        ->alias('products-category');
};
