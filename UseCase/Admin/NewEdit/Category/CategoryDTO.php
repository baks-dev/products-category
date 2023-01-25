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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Category;

use BaksDev\Products\Category\Entity\Event\ProductCategoryEventInterface;
use BaksDev\Products\Category\Entity\Landing\LandingInterface;
use BaksDev\Products\Category\Entity\Offers\OffersInterface;
use BaksDev\Products\Category\Entity\Section\SectionInterface;
use BaksDev\Products\Category\Entity\Seo\SeoInterface;
use BaksDev\Products\Category\Entity\Trans\TransInterface;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Category\Trans\CategoryTransDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Cover\ProductCategoryCoverDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Info\InfoDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing\LandingCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\OffersCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\SectionCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo\SeoCollectionDTO;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryDTO implements ProductCategoryEventInterface
{
    /**
     * Идентификатор события
     * @var CategoryEvent|null
     */
    private ?CategoryEvent $id = null;
    
    /**
     * Идентификатор родительской категории
     * @var ?ParentCategoryUid
     */
    private ?ParentCategoryUid $parent;
    
    
    /**  Сортировка категории */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 500;
    
    /** Настройки локали категории */
    #[Assert\Valid]
    private ArrayCollection $trans;
    
    /** Секции свойств продукта категории */
    #[Assert\Valid]
    private ArrayCollection $sections;
    
    /** Посадочные блоки */
    #[Assert\Valid]
    private ArrayCollection $landings;
    
    /** Торговые предложения */
    #[Assert\Valid]
    private ArrayCollection $offers;
    
    /** SEO категории */
    #[Assert\Valid]
    private ArrayCollection $seo;
    
    #[Assert\Valid]
    private ?ProductCategoryCoverDTO $cover;
    
    
    #[Assert\Valid]
    private InfoDTO $info;
    
    /**
     * @param ParentCategoryUid|null $parent
     */
    public function __construct(
      ?ParentCategoryUid $parent = null,
      //CategoryEvent $event = null,

     // bool $active = true,
     // string $url = null,
    )
    {
        $this->parent = $parent;
        
        $this->cover = new ProductCategoryCoverDTO();
        $this->info = new InfoDTO();
        
        $this->trans = new ArrayCollection();
        $this->landings = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->seo = new ArrayCollection();
    }
    
    /* EVENT  */
    
    /**
     * @return CategoryEvent|null
     */
    public function getEvent() : ?CategoryEvent
    {
        return $this->id;
    }

    public function setId(?CategoryEvent $id) : void
    {
        $this->id = $id;
    }
    

    public function copy() : void
    {
        $this->id = null;
    }
    
    
    
    /* PARENT  */
    
    public function getParent() : ?ParentCategoryUid
    {
        return $this->parent;
    }
    
    /**
     * @param ParentCategoryUid|null $parent
     */
    public function setParent(?ParentCategoryUid $parent) : void
    {
        $this->parent = $parent?->getValue() ? $parent : null;
    }
    
    /* SORT  */
    
    /**
     * @return int
     */
    public function getSort() : int
    {
        return $this->sort;
    }
    
    /**
     * @param int $sort
     */
    public function setSort(int $sort) : void
    {
        $this->sort = $sort;
    }
    

    
    
    
    
    /* INFO  */
    
    
    /**
     * @return InfoDTO
     */
    public function getInfo() : InfoDTO
    {
        return $this->info;
    }
    
    /**
     * @param InfoDTO $info
     */
    public function setInfo(InfoDTO $info) : void
    {
        $this->info = $info;
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getInfoClass() : InfoDTO
    {
        return new InfoDTO();
    }
    



    

    
    
    
    /* Collection Category Translate */
    
    /**
     * @param ArrayCollection $trans
     */
    public function setTrans(ArrayCollection $trans) : void
    {
        $this->trans = $trans;
    }
    
    
    /**
     * @return ArrayCollection
     */
    public function getTrans() : ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->trans) as $locale)
        {
            $CategoryTransDTO = new CategoryTransDTO();
            $CategoryTransDTO->setLocal($locale);
            $this->addTran($CategoryTransDTO);
        }
        
        return $this->trans;
    }
    
    /** Добавляем перевод категории
     * @param CategoryTransDTO $trans
     * @return void
     */
    public function addTran(CategoryTransDTO $trans) : void
    {
        if(!$this->trans->contains($trans))
        {
            $this->trans[] = $trans;
        }
    }

   
    
    /* Collection Category Landing */
    
    /**
     * @return ArrayCollection
     */
    public function getLandings() : ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->landings) as $locale)
        {
            $CategoryLandingDTO = new LandingCollectionDTO();
            $CategoryLandingDTO->setLocal($locale);
            $this->addLanding($CategoryLandingDTO);
        }
        
        return $this->landings;
    }
    
    /**
     * Добавляем коллекцию посадочных блоков
     * @param LandingCollectionDTO $landing
     * @return void
     */
    public function addLanding(LandingCollectionDTO $landing) : void
    {
        if(!$this->landings->contains($landing))
        {
            $this->landings[] = $landing;
        }
    }
    
    public function removeLanding(LandingCollectionDTO $landing) : void
    {
        $this->landings->removeElement($landing);
    }
    

    
    
    /* Collection Category Seo */
    
    
    
    /** Настройки SEO категории
     * @param SeoCollectionDTO $seo
     * @return void
     */
    public function addSeo(SeoCollectionDTO $seo) : void
    {
        if(!$this->seo->contains($seo))
        {
            $this->seo[] = $seo;
        }
    }
    
    public function removeSeo(SeoCollectionDTO $seo) : void
    {
        $this->seo->removeElement($seo);
    }
    

    /**
     * @return ArrayCollection
     */
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

    
    /* Collection Section */
    
    
    
    /** Добавить секцию свойств товара */
    public function addSection(SectionCollectionDTO $section) : void
    {
        if(!$this->sections->contains($section))
        {
            $this->sections[] = $section;
        }
    }
    
    public function removeSection(SectionCollectionDTO $section) : void
    {
        $this->sections->removeElement($section);
    }
    
    /**
     * @return ArrayCollection
     */
    public function getSections() : ArrayCollection
    {
//        if(empty($this->sections->count()))
//        {
//            $this->addSection(new SectionCollectionDTO());
//        }
        
        return $this->sections;
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getSectionClass() : SectionInterface
    {
        return new SectionCollectionDTO();
    }
    
    
    /**
     * @return ArrayCollection
     */
    public function getOffers() : ArrayCollection
    {
        return $this->offers;
    }
    
    
    /** Добавить секцию свойств товара */
    public function addOffer(OffersCollectionDTO $offer) : void
    {
        if(!$this->offers->contains($offer))
        {
            $this->offers[] = $offer;
        }
    }
    
    public function removeOffer(OffersCollectionDTO $offer) : void
    {
        $this->offers->removeElement($offer);
    }
    

    
    
    /* COVER  */
    
    
    public function getCover() : ?ProductCategoryCoverDTO
    {
        return $this->cover;
    }
	
    public function setCover(?ProductCategoryCoverDTO $cover) : void
    {
        $this->cover = $cover;
    }

    
    
    
    //
    //    /**
    //     * @return int
    //     */
    //    public function getSort() : int
    //    {
    //        return $this->sort;
    //    }
    //
    //    /**
    //     * Идентификатор категории
    //     * @return ?CategoryUid
    //     */
    //    public function getCategory() : ?CategoryUid
    //    {
    //        return $this->category;
    //    }
    //
    //    /**
    //     * Идентификатор родительской категории
    //     * @return ?ParentCategoryUid
    //     */
    //    public function getParentCategory() : ?ParentCategoryUid
    //    {
    //        return $this->parentCategory;
    //    }
    //
    //    /**
    //     * @return string
    //     */
    //    public function getUrl() : string
    //    {
    //        return $this->url;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isActive() : bool
    //    {
    //        return $this->active;
    //    }
    //
    //    public function getEvent() : ?CategoryEvent
    //    {
    //        return $this->event;
    //    }
    
    //
    //
    //    /** Торговое предложение товара в разделе
    //     * @param OffersDTO $offer
    //     * @return void
    //     */
    //    public function addOffers(OffersInterface $offer) : void
    //    {
    //        if(!$this->offers->contains($offer))
    //        {
    //            $this->offers[] = $offer;
    //        }
    //    }
    //
    //    /**
    //     * @return ArrayCollection
    //     */
    //    public function getOffers() : ArrayCollection
    //    {
    //        if(empty($this->offers->count()))
    //        {
    //            $this->addOffers(new OffersDTO());
    //        }
    //
    //        return $this->offers;
    //    }
    //
    //
    //    /** Добавить секцию свойств товара
    //     * @param SectionCollectionDTO $section
    //     * @return void
    //     */
    //    public function addSection(SectionInterface $section) : void
    //    {
    //        if(!$this->section->contains($section))
    //        {
    //            $this->section[] = $section;
    //        }
    //    }
    //
    //    /**
    //     * @return ArrayCollection
    //     */
    //    public function getSection() : ArrayCollection
    //    {
    //        if(empty($this->section->count()))
    //        {
    //            $this->addSection(new SectionCollectionDTO());
    //        }
    //
    //        return $this->section;
    //    }
    //
    //
    //
    //
    
}

