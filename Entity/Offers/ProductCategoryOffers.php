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

namespace BaksDev\Products\Category\Entity\Offers;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Field\InputField;
use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Entity\Offers\Variation\ProductCategoryVariation;
use BaksDev\Products\Category\Type\Offers\Id\OffersUid;
use BaksDev\Products\Category\Type\Offers\Id\ProductCategoryOffersUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

// Торговые предложения

#[ORM\Entity]
#[ORM\Table(name: 'product_category_offers')]
class ProductCategoryOffers extends EntityState
{
    public const TABLE = 'product_category_offers';

    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: ProductCategoryOffersUid::TYPE)]
    private readonly ProductCategoryOffersUid $id;

    /** Связь на событие */
    #[ORM\OneToOne(inversedBy: 'offer', targetEntity: ProductCategoryEvent::class)]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id', nullable: true)]
    private ?ProductCategoryEvent $event;

    /** Перевод */
    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Trans\ProductCategoryOffersTrans::class, cascade: ['all'])]
    private Collection $translate;

    /** Справочник */
    #[ORM\Column(type: InputField::TYPE, length: 32, nullable: true, options: ['default' => 'input'])]
    private ?InputField $reference = null;

    /** Загрузка пользовательских изображений */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $image = false;

    /** Торговое предложение с ценой */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $price = false;

    /** Количественный учет товаров */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $quantitative = false;

    /** Торговое предложение с артикулом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $article = false;

    /** Торговое предложение с постфиксом */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $postfix = false;

    /** Множественные варианты в торговом предложении */
    #[ORM\OneToOne(mappedBy: 'offer', targetEntity: ProductCategoryVariation::class, cascade: ['all'])]
    private ?ProductCategoryVariation $variation;

    public function __construct(ProductCategoryEvent $event)
    {
        $this->id = new ProductCategoryOffersUid();
        $this->event = $event;
    }


    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): ProductCategoryOffersUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if ($dto instanceof ProductCategoryOffersInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if ($dto instanceof ProductCategoryOffersInterface || $dto instanceof self)
        {
            if ($dto->isOffer()) {
                return parent::setEntity($dto);
            }

            return false;
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    //    public function removeElement() : void
    //    {
    //        $this->event = null;
    //    }

    //    private function equals($dto) : bool
    //    {
    //        if($dto instanceof ProductCategoryOffersInterface)
    //        {
    //            return  $this->id === $dto->getEquals();
    //        }
    //
    //        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    //    }

    //    /**
    //     * @return string|null
    //     */
    //    public function getReference() : ?string
    //    {
    //        return $this->reference;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isImage() : bool
    //    {
    //        return $this->image;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isArticle() : bool
    //    {
    //        return $this->article;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isPrice() : bool
    //    {
    //        return $this->price;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isMultiple() : bool
    //    {
    //        return $this->multiple;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isQuantitative() : bool
    //    {
    //        return $this->quantitative;
    //    }
    //

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
