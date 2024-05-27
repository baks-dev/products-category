<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Products\Category\Repository\PropertyFieldsCategoryChoice;


use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Entity\ProductCategory;
use BaksDev\Products\Category\Entity\Section\Field\ProductCategorySectionField;
use BaksDev\Products\Category\Entity\Section\Field\Trans\ProductCategorySectionFieldTrans;
use BaksDev\Products\Category\Entity\Section\ProductCategorySection;
use BaksDev\Products\Category\Entity\Section\Trans\ProductCategorySectionTrans;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use BaksDev\Products\Category\Type\Section\Field\Id\ProductCategorySectionFieldUid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PropertyFieldsCategoryChoice implements PropertyFieldsCategoryChoiceInterface
{

    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;


    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {

        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }


    /** Метод возвращает список всех свойств
     */
    public function getPropertyFieldsCollection(ProductCategoryUid $category) : ?array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $local = new Locale($this->translator->getLocale());

        $select = sprintf(
            '
          NEW %s(
              field.id,
              field_trans.name
          )',
            ProductCategorySectionFieldUid::class,
        );

        $qb->select($select);

        $qb->from(ProductCategory::class, 'category');

        $qb->join(
            ProductCategoryEvent::class,
            'category_event',
            'WITH',
            'category_event.id = category.event',
        );

        /* Секции свойств */
        $qb->join(
            ProductCategorySection::class,
            'section',
            'WITH',
            '  section.event = category_event.id',
        );

        /* Перевод секции */
        $qb->join(
            ProductCategorySectionTrans::class,
            'section_trans',
            'WITH',
            'section_trans.section = section.id AND section_trans.local = :locale',
        );

        /* Перевод полей */
        //$qb->addSelect('field.id');
        $qb->join(
            ProductCategorySectionField::class,
            'field',
            'WITH',
            'field.section = section.id',
        );

        $qb->join(
            ProductCategorySectionFieldTrans::class,
            'field_trans',
            'WITH',
            'field_trans.field = field.id AND field_trans.local = :locale',
        );

        $qb->setParameter('locale', $local, Locale::TYPE);

        $qb->where('category.id = :category');
        $qb->setParameter('category', $category, ProductCategoryUid::TYPE);

        $qb->orderBy('section.sort', 'ASC');
        $qb->addOrderBy('field.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }

}