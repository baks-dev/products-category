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

namespace BaksDev\Products\Category\Entity\Event;

use BaksDev\Products\Category\Entity\Category;
use BaksDev\Products\Category\Entity\Cover\ProductCategoryCover;
use BaksDev\Products\Category\Entity\Info\Info;
use BaksDev\Products\Category\Entity\Landing\Landing;
use BaksDev\Products\Category\Entity\Modify\Modify;
use BaksDev\Products\Category\Entity\Offers\Offers;
use BaksDev\Products\Category\Entity\Section\Section;
use BaksDev\Products\Category\Entity\Seo\Seo;
use BaksDev\Products\Category\Entity\Trans\Trans;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Products\Category\Type\Id\CategoryUid;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use BaksDev\Core\Services\EntityEvent\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

//use BaksDev\Core\Entity\EntityEvent;

/* События Category */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_event')]
#[ORM\Index(columns: ['category'])]
#[ORM\Index(columns: ['parent'])]
class ProductCategoryEvent extends EntityEvent
{
    public const TABLE = 'product_category_event';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryEvent::TYPE)]
    protected CategoryEvent $id;
    
    /** ID Category */
    #[ORM\Column(type: CategoryUid::TYPE, nullable: false)]
    protected ?CategoryUid $category = null;
    
    /** ID родительской Category */
    #[ORM\Column(type: ParentCategoryUid::TYPE, nullable: true)]
    protected ?ParentCategoryUid $parent = null;
    
    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false, options: ['default' => 500])]
    protected int $sort = 500;
    
    /** Перевод */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Trans::class, cascade: ['persist', 'remove'])]
    protected Collection $trans;
    
    /** Модификатор */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: Modify::class, cascade: ['persist', 'remove'])]
    protected Modify $modify;
    
    /** Info */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: Info::class, cascade: ['persist', 'remove'])]
    protected ?Info $info;
    
    /** Cover */
    #[ORM\OneToOne(mappedBy: 'event', targetEntity: ProductCategoryCover::class, cascade: ['persist', 'remove'])]
    protected ?ProductCategoryCover $cover = null;
    
    /**  Настройки SEO информации  */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Seo::class, cascade: ['persist', 'remove'])]
    protected Collection $seo;
    
    /** Секции для свойств продукта */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Section::class, cascade: ['persist', 'remove'])]
    protected Collection $sections;
    
    /** Торговые предложения */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Offers::class, cascade: ['persist', 'remove'])]
    protected Collection $offers;
    
    /** Посадочные блоки */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Landing::class, cascade: ['persist', 'remove'])]
    protected Collection $landings;
    
    public function __construct(?ParentCategoryUid $parent = null)
    {
        $this->id = new CategoryEvent();
        
        $this->trans = new ArrayCollection();
        $this->seo = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->landings = new ArrayCollection();
        
        $this->info = new Info($this);
        $this->modify = new Modify($this, new ModifyAction(ModifyActionEnum::NEW));
        
        $this->parent = $parent;
    }
    
//    public function __clone()
//    {
//        $this->id = new CategoryEvent();
//    }
 
	public function __toString() : string
	{
		return $this->id;
	}
	
	/**
     * @return CategoryEvent
     */
    public function getId() : CategoryEvent
    {
        return $this->id;
    }
    
    public function getNameByLocale(Locale $locale) : ?string
    {
        $name = null;
        
        /** @var Trans $trans */
        foreach($this->trans as $trans)
        {
            if($name = $trans->name($locale))
            {
                break;
            }
        }
        
        return $name;
    }
    
    /**
     * @return CategoryUid|null
     */
    public function getCategory() : ?CategoryUid
    {
        return $this->category;
    }
    
    public function setCategory(Category|CategoryUid $category) : void
    {
        $this->category = $category instanceof Category ? $category->getId() : $category;
    }
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof ProductCategoryEventInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {
        if($dto instanceof ProductCategoryEventInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function getUploadCover() : ProductCategoryCover
    {
        return $this->cover ?: $this->cover = new ProductCategoryCover($this);
    }
    
    public function isModifyActionEquals(ModifyActionEnum $action) : bool
    {
        return $this->modify->equals($action);
    }
    
    
    
    //    /**
    //     * @return CategoryUid|null
    //     */
    //    public function getCategory() : ?CategoryUid
    //    {
    //        return $this->category;
    //    }
    //
    //    /**
    //     * @param Category|CategoryUid $category
    //     */
    //    public function setCategory(Category|CategoryUid $category) : void
    //    {
    //        $this->category = $category instanceof Category ? $category->getId() : $category ;
    //    }
    //
    //    /**
    //     * @return CategoryEvent
    //     */
    //    public function getId() : CategoryEvent
    //    {
    //        return $this->id;
    //    }
    
    //    public function clone()
    //    {
    //        $clone = clone $this;
    //        $clone->id = new CategoryEvent();
    //        $clone->modify = new Modify($clone, new ModifyAction(ModifyActionEnum::UPDATE));
    //    }
    
    /* Трейт маппинга Dto->Entity / Entity->Dto */
    //use EntityEvent;
    
    //    public function getCategoryEvent(EventInterface $event) : EventInterface
    //    {
    //
    //
    //
    //
    //
    //
    //        if(method_exists($event, 'setSort'))
    //        {
    //            $event->setSort($this->sort);
    //        }
    //
    //        if(property_exists($event, 'trans') && method_exists($event, 'getTransClass'))
    //        {
    //            /** @var Trans $trans */
    //            foreach($this->trans as $trans)
    //            {
    //                $CategoryTrans = $event->getTransClass();
    //                $event->addTrans($trans->getCategoryTrans($CategoryTrans));
    //            }
    //        }
    //
    //        if(property_exists($event, 'info'))
    //        {
    //            $this->info->getInfo($event->getInfo());
    //        }
    //
    //        if(property_exists($event, 'landing') && method_exists($event, 'getLandingClass'))
    //        {
    //            /** @var Landing $landing */
    //            foreach($this->landings as $landing)
    //            {
    //                $CategoryLanding = $event->getLandingClass();
    //                $event->addLanding($landing->getCategoryLanding($CategoryLanding));
    //            }
    //        }
    //
    //
    //        if(property_exists($event, 'seo') && method_exists($event, 'getSeoClass'))
    //        {
    //            foreach($this->seo as $seo)
    //            {
    //                $CategorySeo = $event->getSeoClass();
    //                $event->addSeo($seo->getCategorySeo($CategorySeo));
    //            }
    //        }
    //
    //        if(property_exists($event, 'section') && method_exists($event, 'getSectionClass'))
    //        {
    //            foreach($this->section as $section)
    //            {
    //                $CategorySection = $event->getSectionClass();
    //                $event->addSection($section->getCategorySection($CategorySection));
    //            }
    //        }
    //
    //
    //
    //
    //
    //
    //        dump('$event');
    //        dd($event);
    //
    //
    //        return $event;
    //    }
    //
    //    public function updCategoryEvent(EventInterface $event) : void
    //    {
    //
    ////        if(property_exists($event, 'sort'))
    ////        {
    ////            $this->sort = $event->sort;
    ////        }
    ////
    ////        if(property_exists($event, 'trans'))
    ////        {
    ////            foreach($event->trans as $trans)
    ////            {
    ////                $CategoryTrans = new Trans($this, $trans->local);
    ////                $CategoryTrans->updCategoryTrans($trans);
    ////                $this->addTrans($CategoryTrans);
    ////            }
    ////        }
    ////
    ////        if(property_exists($event, 'info'))
    ////        {
    ////            $this->info->updInfo($event->info);
    ////
    ////        }
    ////
    ////        if(property_exists($event, 'landing'))
    ////        {
    ////            foreach($event->landing as $landing)
    ////            {
    ////                $CategoryLanding = new Landing($this, $landing->local);
    ////                $CategoryLanding->updCategoryLanding($landing);
    ////                $this->addLanding($CategoryLanding);
    ////            }
    ////        }
    ////
    ////        if(property_exists($event, 'seo'))
    ////        {
    ////            foreach($event->seo as $seo)
    ////            {
    ////                $CategoryLanding = new Seo($this, $seo->local);
    ////                $CategoryLanding->updCategorySeo($seo);
    ////                $this->addSeo($CategoryLanding);
    ////            }
    ////        }
    ////
    ////        if(property_exists($event, 'section'))
    ////        {
    ////            foreach($event->section as $section)
    ////            {
    ////                $CategorySection = new Section($this);
    ////                $CategorySection->updCategorySection($section);
    ////                $this->addSection($CategorySection);
    ////            }
    ////        }
    ////
    ////        if(property_exists($event, 'offers'))
    ////        {
    ////            foreach($event->offers as $offer)
    ////            {
    ////                $CategoryOffer = new Offers($this);
    ////                $CategoryOffer->updCategoryOffer($offer);
    ////                $this->addOffer($CategoryOffer);
    ////            }
    ////        }
    //
    //    }
    //
    //
    //    /** Добавляем перевод категории
    //     * @param Trans $trans
    //     * @return void
    //     */
    //    public function addTrans(Trans $trans) : void
    //    {
    //        if(!$this->trans->contains($trans))
    //        {
    //            $this->trans[] = $trans;
    //        }
    //    }
    //
    //    /**
    //     * Добавляем коллекцию посадочных блоков
    //     * @param Landing $landing
    //     * @return void
    //     */
    //    public function addLanding(Landing $landing) : void
    //    {
    //        if(!$this->landings->contains($landing))
    //        {
    //            $this->landings[] = $landing;
    //        }
    //    }
    //
    //    public function addSeo(Seo $seo) : void
    //    {
    //        if(!$this->seo->contains($seo))
    //        {
    //            $this->seo[] = $seo;
    //        }
    //    }
    //
    //    /** Добавить секцию свойств товара
    //     * @param Section $section
    //     * @return void
    //     */
    //    public function addSection(Section $section) : void
    //    {
    //        if(!$this->section->contains($section))
    //        {
    //            $this->section[] = $section;
    //        }
    //    }
    //
    //    /** Добавить торговое дложение
    //     * @param Offers $offer
    //     * @return void
    //     */
    //    public function addOffer(Offers $offer) : void
    //    {
    //        if(!$this->offers->contains($offer))
    //        {
    //            $this->offers[] = $offer;
    //        }
    //    }
    //
    //    /**
    //     * @return Cover
    //     */
    //    public function getUploadCover() : Cover
    //    {
    //        return $this->cover ? $this->cover : $this->cover = new Cover($this);
    //    }
    
    //
    //    /**
    //     * @return ArrayCollection
    //     */
    //    public function getSection() : ArrayCollection
    //    {
    //        if(empty($this->section->count()))
    //        {
    //            $this->addSection(new Section($this));
    //        }
    //
    //        return $this->section;
    //    }
    
    //    /** Торговое предложение товара в разделе
    //     * @param Offers $offer
    //     * @return void
    //     */
    //    public function addOffer(Offers $offer) : void
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
    //    public function getOffers() : Collection
    //    {
    //        if(empty($this->offers->count()))
    //        {
    //            $this->addOffer(new Offers($this));
    //        }
    //
    //        return $this->offers;
    //    }
    
    //    /**
    //     * @return ArrayCollection
    //     */
    //    public function getLandings() : Collection
    //    {
    //        /* Вычисляем расхождение и добавляем неопределенные локали */
    //        foreach(Locale::diffLocale($this->landings) as $locale)
    //        {
    //            $this->addLanding(new Landing($this, $locale));
    //        }
    //
    //        return $this->landings;
    //    }
    
    //    /**
    //     * @return int
    //     */
    //    public function getSort() : int
    //    {
    //        return $this->sort;
    //    }
    
    //    /**
    //     * @return Info
    //     */
    //    public function getInfo() : Info
    //    {
    //        return $this->info;
    //    }
    //
    //    /**
    //     * @return ParentCategoryUid|null
    //     */
    //    public function getParent() : ?ParentCategoryUid
    //    {
    //        return $this->parent;
    //    }
    
    //
    //
    //
    //    /**
    //     * @param CategoryUid $category
    //     */
    //    public function __construct(Category|CategoryUid $category) {
    //        $this->id = new CategoryEvent();
    //        $this->setCategory($category);
    //
    //        $this->trans = new ArrayCollection(); /* Перевод категории */
    //    }
    //
    //    public function clone(Category|CategoryUid $category) : self
    //    {
    //        $clone = clone $this;
    //        $clone->id = new CategoryEvent();
    //        $clone->setCategory($category);
    //
    //        return $clone;
    //    }
    //
    //    /**
    //     * @param Category|CategoryUid $category
    //     */
    //    protected function setCategory(CategoryUid|Category $category) : void
    //    {
    //        $this->category = $category instanceof Category ? $category->getId() : $category;
    //    }
    //
    //    /**
    //     * @return CategoryEvent
    //     */
    //    public function getId() : CategoryEvent
    //    {
    //        return $this->id;
    //    }
    //
    //
    //
    
    //    public function updEvent(Category|ParentCategoryUid $parent = null, int $sort = 500)
    //    {
    //        $this->parent = $parent instanceof Category ? new ParentCategoryUid($parent->getId()) : $parent;
    //        $this->sort = $sort;
    //    }
    //
    //    /**
    //     * @return CategoryEvent
    //     */
    //    public function getId() : CategoryEvent
    //    {
    //        return $this->id;
    //    }
    
}