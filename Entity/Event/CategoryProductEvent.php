<?php

/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Entity\Event;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Cover\CategoryProductCover;
use BaksDev\Products\Category\Entity\Domains\CategoryProductDomain;
use BaksDev\Products\Category\Entity\Info\CategoryProductInfo;
use BaksDev\Products\Category\Entity\Landing\CategoryProductLanding;
use BaksDev\Products\Category\Entity\Modify\CategoryProductModify;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffers;
use BaksDev\Products\Category\Entity\Section\CategoryProductSection;
use BaksDev\Products\Category\Entity\Seo\CategoryProductSeo;
use BaksDev\Products\Category\Entity\Trans\CategoryProductTrans;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Category\Type\Parent\ParentCategoryProductUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* События Category */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_event')]
#[ORM\Index(columns: ['category'])]
#[ORM\Index(columns: ['parent'])]
class CategoryProductEvent extends EntityState
{
    public const TABLE = 'product_category_event';

    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryProductEventUid::TYPE)]
    private readonly CategoryProductEventUid $id;

    /** ID Category */
    #[ORM\Column(type: CategoryProductUid::TYPE, nullable: false)]
    private ?CategoryProductUid $category = null;

    /** ID родительской Category */
    #[ORM\Column(type: ParentCategoryProductUid::TYPE, nullable: true)]
    private ?ParentCategoryProductUid $parent = null;

    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 500])]
    private int $sort = 500;

    /** Перевод */
    #[ORM\OneToMany(targetEntity: CategoryProductDomain::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $domain;


    /** Перевод */
    #[ORM\OneToMany(targetEntity: CategoryProductTrans::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $translate;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: CategoryProductModify::class, mappedBy: 'event', cascade: ['all'])]
    private CategoryProductModify $modify;

    /** Info */
    #[ORM\OneToOne(targetEntity: CategoryProductInfo::class, mappedBy: 'event', cascade: ['all'])]
    private ?CategoryProductInfo $info;

    /** Cover */
    #[ORM\OneToOne(targetEntity: CategoryProductCover::class, mappedBy: 'event', cascade: ['all'])]
    private ?CategoryProductCover $cover = null;

    /**  Настройки SEO информации  */
    #[ORM\OneToMany(targetEntity: CategoryProductSeo::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $seo;

    /** Секции для свойств продукта */
    #[ORM\OneToMany(targetEntity: CategoryProductSection::class, mappedBy: 'event', cascade: ['all'])]
    #[ORM\OrderBy(['sort' => 'ASC'])]
    private Collection $section;

    /** Посадочные блоки */
    #[ORM\OneToMany(targetEntity: CategoryProductLanding::class, mappedBy: 'event', cascade: ['all'])]
    private Collection $landing;

    /** Торговые предложения */
    #[ORM\OneToOne(targetEntity: CategoryProductOffers::class, mappedBy: 'event', cascade: ['all'])]
    private ?CategoryProductOffers $offer;


    public function __construct(?ParentCategoryProductUid $parent = null)
    {
        $this->id = new CategoryProductEventUid();
        $this->info = new CategoryProductInfo($this);
        $this->modify = new CategoryProductModify($this);
        $this->parent = $parent;
    }


    public function __toString(): string
    {
        return $this->id;
    }

    public function getMain(): CategoryProductUid
    {
        return $this->category;
    }


    public function getId(): CategoryProductEventUid
    {
        return $this->id;
    }

    public function getNameByLocale(Locale $locale): ?string
    {
        $name = null;

        /** @var CategoryProductTrans $trans */
        foreach($this->translate as $trans)
        {
            if($name = $trans->getNameByLocale($locale))
            {
                break;
            }
        }

        return $name;
    }


    public function getCategory(): ?CategoryProductUid
    {
        return $this->category;
    }


    public function setMain(CategoryProduct|CategoryProductUid $category): void
    {
        $this->category = $category instanceof CategoryProduct ? $category->getId() : $category;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryProductEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryProductEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getUploadCover(): CategoryProductCover
    {
        return $this->cover ?: $this->cover = new CategoryProductCover($this);
    }

}
