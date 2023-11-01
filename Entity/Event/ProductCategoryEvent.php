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

namespace BaksDev\Products\Category\Entity\Event;


use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Cover\ProductCategoryCover;
use BaksDev\Products\Category\Entity\Info\ProductCategoryInfo;
use BaksDev\Products\Category\Entity\Landing\ProductCategoryLanding;
use BaksDev\Products\Category\Entity\Modify\ProductCategoryModify;
use BaksDev\Products\Category\Entity\Offers\ProductCategoryOffers;
use BaksDev\Products\Category\Entity\ProductCategory;
use BaksDev\Products\Category\Entity\Section\ProductCategorySection;
use BaksDev\Products\Category\Entity\Seo\ProductCategorySeo;
use BaksDev\Products\Category\Entity\Trans\ProductCategoryTrans;
use BaksDev\Products\Category\Type\Event\ProductCategoryEventUid;
use BaksDev\Products\Category\Type\Id\ProductCategoryUid;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* События Category */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_event')]
#[ORM\Index(columns: ['category'])]
#[ORM\Index(columns: ['parent'])]
class ProductCategoryEvent extends EntityState
{
	public const TABLE = 'product_category_event';
	
	/** ID */
	#[ORM\Id]
	#[ORM\Column(type: ProductCategoryEventUid::TYPE)]
	private readonly ProductCategoryEventUid $id;
	
	/** ID Category */
	#[ORM\Column(type: ProductCategoryUid::TYPE, nullable: false)]
	private ?ProductCategoryUid $category = null;
	
	/** ID родительской Category */
	#[ORM\Column(type: ProductParentCategoryUid::TYPE, nullable: true)]
	private ?ProductParentCategoryUid $parent = null;
	
	/** Сортировка */
	#[ORM\Column(type: Types::SMALLINT, length: 3, options: ['default' => 500])]
	private int $sort = 500;
	
	/** Перевод */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: ProductCategoryTrans::class, cascade: ['all'])]
	private Collection $translate;
	
	/** Модификатор */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: ProductCategoryModify::class, cascade: ['all'])]
	private ProductCategoryModify $modify;
	
	/** Info */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: ProductCategoryInfo::class, cascade: ['all'])]
	private ?ProductCategoryInfo $info;
	
	/** Cover */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: ProductCategoryCover::class, cascade: ['all'])]
	private ?ProductCategoryCover $cover = null;
	
	/**  Настройки SEO информации  */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: ProductCategorySeo::class, cascade: ['all'])]
	private Collection $seo;
	
	/** Секции для свойств продукта */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: ProductCategorySection::class, cascade: ['all'])]
	#[ORM\OrderBy(['sort' => 'ASC'])]
	private Collection $section;
	
	/** Посадочные блоки */
	#[ORM\OneToMany(mappedBy: 'event', targetEntity: ProductCategoryLanding::class, cascade: ['all'])]
	private Collection $landing;
	
	/** Торговые предложения */
	#[ORM\OneToOne(mappedBy: 'event', targetEntity: ProductCategoryOffers::class, cascade: ['all'])]
	private ?ProductCategoryOffers $offer;
	
	
	public function __construct(?ProductParentCategoryUid $parent = null)
	{
		$this->id = new ProductCategoryEventUid();
		$this->info = new ProductCategoryInfo($this);
		$this->modify = new ProductCategoryModify($this);
		$this->parent = $parent;
	}
	
	
	public function __toString(): string
	{
		return $this->id;
	}
	
	
	public function getId() : ProductCategoryEventUid
	{
		return $this->id;
	}
	
	public function getNameByLocale(Locale $locale) : ?string
	{
		$name = null;
		
		/** @var ProductCategoryTrans $trans */
		foreach($this->translate as $trans)
		{
			if($name = $trans->getNameByLocale($locale))
			{
				break;
			}
		}
		
		return $name;
	}
	
	
	public function getCategory() : ?ProductCategoryUid
	{
		return $this->category;
	}


	public function setMain(ProductCategory|ProductCategoryUid $category) : void
	{
		$this->category = $category instanceof ProductCategory ? $category->getId() : $category;
	}
	
	
	public function getDto($dto): mixed
	{
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

		if($dto instanceof ProductCategoryEventInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto): mixed
	{
		if($dto instanceof ProductCategoryEventInterface || $dto instanceof self)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function getUploadCover() : ProductCategoryCover
	{
		return $this->cover ?: $this->cover = new ProductCategoryCover($this);
	}

}