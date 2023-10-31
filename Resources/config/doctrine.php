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

use BaksDev\Products\Category\Type\Event\ProductCategoryEventType;
use BaksDev\Products\Category\Type\Event\ProductCategoryEventUid;
use BaksDev\Products\Category\Type\Id\ProductCategoryType;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use BaksDev\Products\Category\Type\Landing\Id\ProductCategoryLandingType;
use BaksDev\Products\Category\Type\Landing\Id\ProductCategoryLandingUid;
use BaksDev\Products\Category\Type\Offers\Id\ProductCategoryOffersType;
use BaksDev\Products\Category\Type\Offers\Id\ProductCategoryOffersUid;
use BaksDev\Products\Category\Type\Offers\Modification\ProductCategoryModificationType;
use BaksDev\Products\Category\Type\Offers\Modification\ProductCategoryModificationUid;
use BaksDev\Products\Category\Type\Offers\Type\ProductCategoryModificationTypeType;
use BaksDev\Products\Category\Type\Offers\Type\ProductCategoryModificationTypeUid;
use BaksDev\Products\Category\Type\Offers\Variation\ProductCategoryVariationType;
use BaksDev\Products\Category\Type\Offers\Variation\ProductCategoryVariationUid;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryType;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use BaksDev\Products\Category\Type\Section\Field\Id\ProductCategorySectionFieldType;
use BaksDev\Products\Category\Type\Section\Field\Id\ProductCategorySectionFieldUid;
use BaksDev\Products\Category\Type\Section\Id\ProductCategorySectionType;
use BaksDev\Products\Category\Type\Section\Id\ProductCategorySectionUid;
use BaksDev\Products\Category\Type\Settings\ProductCategorySettingsIdentifier;
use BaksDev\Products\Category\Type\Settings\ProductCategorySettingsType;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine) {
	
	$doctrine->dbal()->type(ProductCategorySettingsIdentifier::TYPE)->class(ProductCategorySettingsType::class);
	$doctrine->dbal()->type(ProductCategorySectionFieldUid::TYPE)->class(ProductCategorySectionFieldType::class);
	
	$doctrine->dbal()->type(ProductCategoryOffersUid::TYPE)->class(ProductCategoryOffersType::class);
	$container->services()->set(ProductCategoryOffersUid::class)
		->tag('controller.argument_value_resolver');
	
	$doctrine->dbal()->type(ProductCategoryLandingUid::TYPE)->class(ProductCategoryLandingType::class);
	$doctrine->dbal()->type(ProductCategoryUid::TYPE)->class(ProductCategoryType::class);
	$doctrine->dbal()->type(ProductCategorySectionUid::TYPE)->class(ProductCategorySectionType::class);
	$doctrine->dbal()->type(ProductParentCategoryUid::TYPE)->class(ProductParentCategoryType::class);
	$doctrine->dbal()->type(ProductCategoryEventUid::TYPE)->class(ProductCategoryEventType::class);
	$doctrine->dbal()->type(ProductCategoryVariationUid::TYPE)->class(ProductCategoryVariationType::class);
	$doctrine->dbal()->type(ProductCategoryModificationUid::TYPE)->class(ProductCategoryModificationType::class);
	$doctrine->dbal()->type(ProductCategoryModificationTypeUid::TYPE)->class(ProductCategoryModificationTypeType::class);



    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $MODULE = substr(__DIR__, 0, strpos(__DIR__, "Resources"));

    $emDefault->mapping('products-category')
		->type('attribute')
		->dir($MODULE.'Entity')
		->isBundle(false)
		->prefix('BaksDev\Products\Category\Entity')
		->alias('products-category')
	;
};