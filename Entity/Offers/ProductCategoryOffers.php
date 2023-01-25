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

namespace BaksDev\Products\Category\Entity\Offers;

use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\Type\Offers\Id\OffersUid;
use BaksDev\Core\Services\EntityEvent\EntityEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Торговые предложения */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_offers')]
class ProductCategoryOffers extends EntityEvent
{
    public const TABLE = 'product_category_offers';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: OffersUid::TYPE)]
    protected OffersUid $id;
    
    /** ID торгового предложения */
    #[ORM\Column(type: OffersUid::TYPE, nullable: true)]
    protected ?OffersUid $field = null;
    
    /** Связь на событие */
    #[ORM\ManyToOne(targetEntity: Event::class, cascade: ["all"], inversedBy: "offers")]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: true)]
    protected ?Event $event;
    
    /** Перевод */
    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Trans\Trans::class, cascade: ['persist', 'remove'])]
    protected Collection $trans;
    
    /** Справочник */
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    protected ?string $reference = null;
    
    /** Загрузка пользовательских изображений */
    #[ORM\Column(type: Types::BOOLEAN)]
    protected bool $image = false;
    
    /** Торговое предложение с ценой */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $price = false;
    
    /** Количественный учет товаров */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $quantitative = false;
    
    /** Торговое предложение с артикулом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $article = false;
    
    /** Множественный выбор */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $multiple = false;
    
    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false, options: ['default' => 500])]
    protected int $sort = 100;
    

    public function __construct(Event $event)
    {
        $this->id = new OffersUid();
        $this->event = $event;
        
        $this->trans = new ArrayCollection();
        //$this->getTrans();
        
    }
    
//    public function __clone()
//    {
//        $this->id = new OffersUid();
//    }
	
	public function __toString() : string
	{
		return $this->id;
	}
	
    /**
     * @return OffersUid
     */
    public function getId() : OffersUid
    {
        return $this->id;
    }
    
    
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof OffersInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * Метод присваивает свойствам значения из объекта DTO
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {
        if($dto instanceof OffersInterface)
        {
            if($this->field === null)
            {
                $this->field = $this->id;
            }
            
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    public function removeElement() : void
    {
        $this->event = null;
    }
    
    
    protected function equals($dto) : bool
    {
        if($dto instanceof OffersInterface)
        {
            return  $this->id === $dto->getEquals();
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * @return string|null
     */
    public function getReference() : ?string
    {
        return $this->reference;
    }
    
    /**
     * @return bool
     */
    public function isImage() : bool
    {
        return $this->image;
    }
    
    /**
     * @return bool
     */
    public function isArticle() : bool
    {
        return $this->article;
    }
    
    /**
     * @return bool
     */
    public function isPrice() : bool
    {
        return $this->price;
    }
    
    /**
     * @return bool
     */
    public function isMultiple() : bool
    {
        return $this->multiple;
    }
    
    /**
     * @return bool
     */
    public function isQuantitative() : bool
    {
        return $this->quantitative;
    }
  

//    public function updCategoryOffer(OffersInterface $offer) : void
//    {
//        if(property_exists($offer, 'trans'))
//        {
//            foreach($offer->trans as $trans)
//            {
//                $categorySectionTrans = new Offers\Trans($this, $trans->local);
//                $categorySectionTrans->updOfferTrans($trans);
//                $this->addTrans($categorySectionTrans);
//            }
//        }
//
//        if(property_exists($offer, 'reference'))
//        {
//            $this->reference = $offer->reference;
//        }
//
//        if(property_exists($offer, 'image'))
//        {
//            $this->image = $offer->image;
//        }
//
//        if(property_exists($offer, 'price'))
//        {
//            $this->price = $offer->price;
//        }
//
//        if(property_exists($offer, 'multiple'))
//        {
//            $this->multiple = $offer->multiple;
//        }
//
//        if(property_exists($offer, 'sort'))
//        {
//            $this->sort = $offer->sort;
//        }
//    }
    
//    /** Добавляем перевод торгового предложения
//     * @param Section\Trans $trans
//     * @return void
//     */
//    public function addTrans(Offers\Trans $trans) : void
//    {
//        if(!$this->trans->contains($trans))
//        {
//            $this->trans[] = $trans;
//        }
//    }
    
    
    
    //
    //
    //    /**
    //     * @return ArrayCollection
    //     */
    //    public function getTrans() : Collection
    //    {
    //        /* Вычисляем расхождение и добавляем неопределенные локали */
    //        foreach(Locale::diffLocale($this->trans) as $locale)
    //        {
    //            $this->addTrans(new Offers\Trans($this, $locale));
    //        }
    //
    //        return $this->trans;
    //    }
    //
    //
    //    /** Добавляем перевод категории
    //     * @param Trans $trans
    //     * @return void
    //     */
    //    public function addTrans(Offers\Trans $trans) : void
    //    {
    //        if(!$this->trans->contains($trans))
    //        {
    //            $this->trans[] = $trans;
    //        }
    //    }
    //
    //
    
    //    public function __construct(Event|CategoryEvent $event)
    //    {
    //        $this->id = new OffersUid();
    //        $this->event = $event instanceof Event ? $event->getId() : $event;
    //    }
    //
    //
    //    /**
    //     * @param string|null $reference
    //     * @param bool $isImage
    //     * @param bool $isPrice
    //     * @param bool $isMultiple
    //     * @param int $sort
    //     */
    //    public function addOffers(
    //      bool $isImage,
    //      bool $isPrice,
    //      bool $isMultiple,
    //      int $sort,
    //      string $reference = null,
    //    ) : void
    //    {
    //        $this->reference = $reference;
    //        $this->isImage = $isImage;
    //        $this->isPrice = $isPrice;
    //        $this->isMultiple = $isMultiple;
    //        $this->sort = $sort;
    //    }
    //
    //    /**
    //     * @return OffersUid
    //     */
    //    public function getId() : OffersUid
    //    {
    //        return $this->id;
    //    }
    
}