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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit;

use BaksDev\Products\Category\Entity\Event\ProductCategoryEventInterface;
use BaksDev\Products\Category\Type\Event\ProductCategoryEventUid;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Trans\CategoryTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Cover\ProductCategoryCoverDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Info\InfoDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing\LandingCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\ProductCategoryOffersDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\SectionCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo\SeoCollectionDTO;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductCategoryDTO implements ProductCategoryEventInterface
{
	/** Идентификатор события */
	#[Assert\Uuid]
	private ?ProductCategoryEventUid $id = null;
	
	/** Идентификатор родительской категории */
	#[Assert\Uuid]
	private ?ProductParentCategoryUid $parent;
	
	/**  Сортировка категории */
	#[Assert\NotBlank]
	#[Assert\Range(min: 0, max: 999)]
	private int $sort = 500;
	
	/** Настройки локали категории */
	#[Assert\Valid]
	private ArrayCollection $translate;
	
	/** Секции свойств продукта категории */
	#[Assert\Valid]
	private ArrayCollection $section;
	
	/** Посадочные блоки */
	#[Assert\Valid]
	private ArrayCollection $landing;
	
	/** Торговые предложения */
	#[Assert\Valid]
	private ?ProductCategoryOffersDTO $offer = null;
	
	/** Настройки SEO категории */
	#[Assert\Valid]
	private ArrayCollection $seo;
	
	/** Обложка категории */
	#[Assert\Valid]
	private ?ProductCategoryCoverDTO $cover;
	
	/** Неизменяемые свойства категории */
	#[Assert\Valid]
	private InfoDTO $info;
	
	/**  Модификатор события  */
	#[Assert\Valid]
	private readonly Modify\ProductCategoryModifyDTO $modify;
	
	
	public function __construct(
		?ProductParentCategoryUid $parent = null,
		//CategoryEvent $event = null,
		
		// bool $active = true,
		// string $url = null,
	)
	{
		$this->parent = $parent;
		
		$this->cover = new ProductCategoryCoverDTO();
		$this->info = new InfoDTO();
		$this->modify = new Modify\ProductCategoryModifyDTO();
		
		$this->translate = new ArrayCollection();
		$this->landing = new ArrayCollection();
		$this->section = new ArrayCollection();
		$this->offer = new Offers\ProductCategoryOffersDTO();
		$this->seo = new ArrayCollection();
	}
	
	/** Идентификатор события */
	
	public function getEvent() : ?ProductCategoryEventUid
	{
		return $this->id;
	}
	
	/** Идентификатор родительской категории */
	
	public function getParent() : ?ProductParentCategoryUid
	{
		return $this->parent;
	}
	
	
	public function setParent(?ProductParentCategoryUid $parent) : void
	{
		$this->parent = $parent?->getValue() ? $parent : null;
	}
	
	/**  Сортировка категории */
	
	public function getSort() : int
	{
		return $this->sort;
	}
	
	
	public function setSort(int $sort) : void
	{
		$this->sort = $sort;
	}
	
	
	/** Неизменяемые свойства категории INFO */
	
	public function getInfo() : InfoDTO
	{
		return $this->info;
	}
	
	
	public function setInfo(InfoDTO $info) : void
	{
		$this->info = $info;
	}
	
	
	/** Настройки локали категории */
	
	
	public function getTranslate() : ArrayCollection
	{
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->translate) as $locale)
		{
			$CategoryTransDTO = new CategoryTransDTO();
			$CategoryTransDTO->setLocal($locale);
			$this->addTranslate($CategoryTransDTO);
		}

		return $this->translate;
	}
	
	public function addTranslate(CategoryTransDTO $trans) : void
	{
        if(empty($trans->getLocal()->getLocalValue()))
        {
            return;
        }

		if(!$this->translate->contains($trans))
		{
			$this->translate->add($trans);
		}
	}
	
	public function removeTranslate(CategoryTransDTO $trans) : void
	{
		$this->translate->removeElement($trans);
	}



	
	/** Посадочные блоки */
	
	public function getLanding() : ArrayCollection
	{
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->landing) as $locale)
		{
			$CategoryLandingDTO = new LandingCollectionDTO();
			$CategoryLandingDTO->setLocal($locale);
			$this->addLanding($CategoryLandingDTO);
		}
		
		return $this->landing;
	}
	
	public function addLanding(LandingCollectionDTO $landing) : void
	{
        if(empty($landing->getLocal()->getLocalValue()))
        {
            return;
        }

		if(!$this->landing->contains($landing))
		{
			$this->landing->add($landing);
		}
	}
	
	public function removeLanding(LandingCollectionDTO $landing) : void
	{
		$this->landing->removeElement($landing);
	}
	
	
	/** Настройки SEO категории */
	
	
	public function getSeo() : ArrayCollection
	{
		
		/* Вычисляем расхождение и добавляем неопределенные локали */
		foreach(Locale::diffLocale($this->seo) as $locale)
		{
			$CategorySeoDTO = new SeoCollectionDTO();
			$CategorySeoDTO->setLocal($locale);
			$this->addSeo($CategorySeoDTO);
		}
		
		return $this->seo;
	}
	
	public function addSeo(SeoCollectionDTO $seo) : void
	{
        if(empty($seo->getLocal()->getLocalValue()))
        {
            return;
        }

		if(!$this->seo->contains($seo))
		{
			$this->seo[] = $seo;
		}
	}
	
	public function removeSeo(SeoCollectionDTO $seo) : void
	{
		$this->seo->removeElement($seo);
	}
	
	
	/** Секции свойств продукта категории */
	
	public function getSection() : ArrayCollection
	{
		return $this->section;
	}
	
	public function addSection(SectionCollectionDTO $section) : void
	{
		if(!$this->section->contains($section))
		{
			$this->section->add($section);
		}
	}
	
	public function removeSection(SectionCollectionDTO $section) : void
	{
		$this->section->removeElement($section);
	}
	
	
	/** Торговые предложения */
	
	public function getOffer() : ?ProductCategoryOffersDTO
	{
		return $this->offer;
	}
	
	public function setOffer(ProductCategoryOffersDTO $offer) : void
	{
		$this->offer = $offer;
	}
	
	/**  Модификатор события  */
	
	public function getModify() : Modify\ProductCategoryModifyDTO
	{
		return $this->modify;
	}
	
	
	/*public function getOffer() : ArrayCollection
	{
		return $this->offer;
	}
	
	public function addOffer(ProductCategoryOffersDTO $offer) : void
	{
		if(!$this->offer->contains($offer))
		{
			$this->offer->add($offer);
		}
	}
	
	public function removeOffer(ProductCategoryOffersDTO $offer) : void
	{
		$this->offer->removeElement($offer);
	}*/
	
	
	/** Обложка категории */
	
	public function getCover() : ?ProductCategoryCoverDTO
	{
		return $this->cover;
	}
	
	public function setCover(?ProductCategoryCoverDTO $cover) : void
	{
		$this->cover = $cover;
	}
	
}

