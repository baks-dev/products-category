<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers;

use BaksDev\Core\Type\Field\InputField;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Offers\CategoryProductOffersInterface;
use BaksDev\Products\Category\Type\Offers\Id\CategoryProductOffersUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Trans\CategoryProductOffersTransDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryProductOffersDTO implements CategoryProductOffersInterface
{
    #[Assert\Uuid]
    private ?CategoryProductOffersUid $id = null;

    /** Флаг, что товары в категории с торговым предложением */
    private bool $offer = false;

    /** Справочник */
    private ?InputField $reference = null;

    /** Загрузка пользовательских изображений */
    private bool $image = false;

    /** Торговое предложение с ценой */
    private bool $price = false;

    /** Количественный учет */
    private bool $quantitative = false;

    /** Торговое предложение с артикулом */
    private bool $article = false;

    /** Торговое предложение с постфиксом */
    private bool $postfix = false;

    /** Свойство группировки карточки */
    private bool $card = true;

    /** Настройки локали торгового предложения */
    #[Assert\Valid]
    private ArrayCollection $translate;

    /** Множественные варианты торговых предложений  */
    private ?Variation\CategoryProductVariationDTO $variation;


    public function __construct()
    {
        $this->translate = new ArrayCollection();
        $this->variation = new Variation\CategoryProductVariationDTO();
    }


    /**
     * Id
     */
    public function getId(): ?CategoryProductOffersUid
    {
        return $this->id;
    }

    /** Справочник */

    public function getReference(): ?InputField
    {
        return $this->reference?->getType() ? $this->reference : null;
    }


    public function setReference(?InputField $reference): void
    {
        $this->reference = $reference;
    }

    //	/**  Сортировка */
    //
    //    public function getSort() : int
    //    {
    //        return $this->sort;
    //    }
    //
    //    public function setSort(int $sort) : void
    //    {
    //        $this->sort = $sort;
    //    }

    /** Загрузка пользовательских изображений */

    public function getImage(): bool
    {
        return $this->image;
    }


    public function setImage(bool $image): void
    {
        $this->image = $image;
    }


    /** Торговое предложение с ценой */

    public function getPrice(): bool
    {
        return $this->price;
    }


    public function setPrice(bool $price): void
    {
        $this->price = $price;
    }

    //	/** Множественный выбор */
    //
    //    public function getMultiple() : bool
    //    {
    //        return $this->multiple;
    //    }
    //
    //    public function setMultiple(bool $multiple) : void
    //    {
    //        $this->multiple = $multiple;
    //    }

    /** Торговое предложение с артикулом */

    public function getArticle(): bool
    {
        return $this->article;
    }


    public function setArticle(bool $article): void
    {
        $this->article = $article;
    }


    /** Настройки локали торгового предложения */

    public function getTranslate(): ArrayCollection
    {
        if(!$this->translate->isEmpty())
        {
            $this->offer = true;
        }

        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $OffersTransDTO = new CategoryProductOffersTransDTO();
            $OffersTransDTO->setLocal($locale);
            $this->addTranslate($OffersTransDTO);
        }

        return $this->translate;
    }


    public function addTranslate(CategoryProductOffersTransDTO $trans): void
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


    public function removeTranslate(CategoryProductOffersTransDTO $trans): void
    {
        $this->translate->removeElement($trans);
    }


    /** Количественный учет */

    public function getQuantitative(): bool
    {
        return $this->quantitative;
    }


    public function setQuantitative(bool $quantitative): void
    {
        $this->quantitative = $quantitative;
    }


    /** Множественные варианты торговых предложений  */

    public function getVariation(): ?Variation\CategoryProductVariationDTO
    {
        return $this->variation;
    }


    public function setVariation(Variation\CategoryProductVariationDTO $variation): void
    {
        $this->variation = $variation;
    }


    /** Флаг, что товары в категории с торговым предложением */

    public function isOffer(): bool
    {
        return $this->offer;
    }

    public function resetVariation(): void
    {
        $this->variation->resetModification();

        if($this->variation->isVariation() === false)
        {
            $this->variation = null;
        }
    }


    public function setOffer(bool $offer): void
    {
        $this->offer = $offer;
    }


    public function isPostfix(): bool
    {
        return $this->postfix;
    }


    /** Торговое предложение с постфиксом */

    public function setPostfix(bool $postfix): void
    {
        $this->postfix = $postfix;
    }

    /**
     * Card
     */
    public function getCard(): bool
    {
        return $this->card;
    }

    public function setCard(bool $card): self
    {
        $this->card = $card;
        return $this;
    }

}
