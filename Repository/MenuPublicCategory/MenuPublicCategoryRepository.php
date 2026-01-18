<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\MenuPublicCategory;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Cover\CategoryProductCover;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryResult\MenuPublicCategoryDTO;
use BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryResult\MenuPublicCategoryResult;
use BaksDev\Products\Product\Repository\Cards\ModelsByCategory\ModelsByCategoryInterface;


final  class MenuPublicCategoryRepository implements MenuPublicCategoryInterface
{
    private int $max = 6;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly ModelsByCategoryInterface $ModelsByCategoryInterface,
    ) {}

    /**
     * Метод позволяет указать количество товаров в категории
     */
    public function maxResult(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Метод возвращает все категории меню
     */
    public function findAll(bool $products = false): MenuPublicCategoryResult|bool
    {

        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        // Категория
        $dbal
            ->select('category.id')
            ->addSelect('category.event')
            ->from(CategoryProduct::class, 'category');

        $dbal
            ->addSelect('category_event.sort')
            ->addSelect('category_event.parent')
            ->joinRecursive(
                'category',
                CategoryProductEvent::class,
                'category_event',
                'category_event.id = category.event',
            );


        $dbal
            ->addSelect('category_info.url AS category_url')
            ->join(
                'category_event',
                CategoryProductInfo::class,
                'category_info',
                '
                    category_info.event = category.event AND 
                    category_info.active IS TRUE',
            );

        $dbal
            ->addSelect('category_trans.name AS category_name')
            ->leftJoin(
                'category',
                CategoryProductTrans::class,
                'category_trans',
                'category_trans.event = category.event AND category_trans.local = :local',
            );


        $dbal
            ->addSelect("CONCAT ('/upload/".$dbal->table(CategoryProductCover::class)."' , '/', category_cover.name) AS category_cover_image")
            ->addSelect('category_cover.cdn AS category_cover_cdn')
            ->addSelect('category_cover.ext AS category_cover_ext')
            ->leftJoin(
                'category',
                CategoryProductCover::class,
                'category_cover',
                'category_cover.event = category.event',
            );


        $result = $dbal
            ->enableCache('products-category')
            ->findAllRecursive(['parent' => 'id']);

        usort($result, function($a, $b) {
            return $a['sort'] - $b['sort'];
        });

        /**
         * Добавляем категории в результат
         */

        $MenuPublicCategoryResult = new MenuPublicCategoryResult($result);

        /** Если не требуется продукция по категориям */
        if(false === $products)
        {
            return $MenuPublicCategoryResult;
        }

        /**
         * Получаем продукцию по категориям
         */

        /** @var MenuPublicCategoryDTO $category */
        foreach($MenuPublicCategoryResult as $category)
        {
            $ModelOrProductByCategoryResult = $this->ModelsByCategoryInterface
                ->inCategories($category->getAllCategoryIdentifier())
                ->maxResult($this->max)
                ->findAll();

            $category->setProducts($ModelOrProductByCategoryResult);
        }

        return $MenuPublicCategoryResult;

        /**
         * @example Пример обработки
         * @var MenuPublicCategoryDTO $item
         */
        //        foreach($MenuPublicCategoryResult as $item)
        //        {
        //            if(false === $item->isExistsProduct())
        //            {
        //                continue;
        //            }
        //
        //            dump($item->getLevel().' - '.$item->getCategoryName());
        //
        //            foreach($item->getProducts() as $product)
        //            {
        //                dump($product->getName());
        //            }
        //
        //        }

    }
}