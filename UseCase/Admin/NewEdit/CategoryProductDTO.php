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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Event\CategoryProductEventInterface;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use BaksDev\Products\Category\Type\Parent\ParentCategoryProductUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Cover\CategoryProductCoverDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Info\CategoryProductInfoDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing\CategoryProductLandingCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\CategoryProductOffersDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\CategoryProductSectionCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo\CategoryProductSeoCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Trans\CategoryProductTransDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryProductDTO implements CategoryProductEventInterface
{
    /** Идентификатор события */
    #[Assert\Uuid]
    private ?CategoryProductEventUid $id = null;

    /** Идентификатор родительской категории */
    #[Assert\Uuid]
    private ?ParentCategoryProductUid $parent;

    /**  Сортировка категории */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 500;

    /** Настройки локали категории */
    #[Assert\Valid]
    private ArrayCollection $translate;

    /** Настройки локали категории */
    #[Assert\Valid]
    private ArrayCollection $domain;


    /** Секции свойств продукта категории */
    #[Assert\Valid]
    private ArrayCollection $section;

    /** Посадочные блоки */
    #[Assert\Valid]
    private ArrayCollection $landing;

    /** Торговые предложения */
    #[Assert\Valid]
    private ?CategoryProductOffersDTO $offer;

    /** Настройки SEO категории */
    #[Assert\Valid]
    private ArrayCollection $seo;

    /** Обложка категории */
    #[Assert\Valid]
    private ?CategoryProductCoverDTO $cover;

    /** Неизменяемые свойства категории */
    #[Assert\Valid]
    private CategoryProductInfoDTO $info;

    /**  Модификатор события  */
    #[Assert\Valid]
    private readonly Modify\CategoryProductModifyDTO $modify;


    public function __construct(
        ?ParentCategoryProductUid $parent = null,
        //CategoryEvent $event = null,

        // bool $active = true,
        // string $url = null,
    )
    {
        $this->parent = $parent;

        $this->cover = new CategoryProductCoverDTO();
        $this->info = new CategoryProductInfoDTO();
        $this->modify = new Modify\CategoryProductModifyDTO();
        $this->offer = new Offers\CategoryProductOffersDTO();

        $this->translate = new ArrayCollection();
        $this->landing = new ArrayCollection();
        $this->section = new ArrayCollection();
        $this->seo = new ArrayCollection();
        $this->domain = new ArrayCollection();

    }

    /** Идентификатор события */

    public function getEvent(): ?CategoryProductEventUid
    {
        return $this->id;
    }

    /** Идентификатор родительской категории */

    public function getParent(): ?ParentCategoryProductUid
    {
        return $this->parent;
    }


    public function setParent(?ParentCategoryProductUid $parent): void
    {
        $this->parent = $parent?->getValue() ? $parent : null;
    }

    /**  Сортировка категории */

    public function getSort(): int
    {
        return $this->sort;
    }


    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }


    /** Неизменяемые свойства категории INFO */

    public function getInfo(): CategoryProductInfoDTO
    {
        return $this->info;
    }


    public function setInfo(CategoryProductInfoDTO $info): void
    {
        $this->info = $info;
    }


    /** Настройки локали категории */


    public function getTranslate(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->translate) as $locale)
        {
            $CategoryTransDTO = new CategoryProductTransDTO();
            $CategoryTransDTO->setLocal($locale);
            $this->addTranslate($CategoryTransDTO);
        }

        return $this->translate;
    }

    public function addTranslate(CategoryProductTransDTO $trans): void
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

    public function removeTranslate(CategoryProductTransDTO $trans): void
    {
        $this->translate->removeElement($trans);
    }


    /** Посадочные блоки */

    public function getLanding(): ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->landing) as $locale)
        {
            $CategoryLandingDTO = new CategoryProductLandingCollectionDTO();
            $CategoryLandingDTO->setLocal($locale);
            $this->addLanding($CategoryLandingDTO);
        }

        return $this->landing;
    }

    public function addLanding(CategoryProductLandingCollectionDTO $landing): void
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

    public function removeLanding(CategoryProductLandingCollectionDTO $landing): void
    {
        $this->landing->removeElement($landing);
    }


    /** Настройки SEO категории */


    public function getSeo(): ArrayCollection
    {

        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->seo) as $locale)
        {
            $CategorySeoDTO = new CategoryProductSeoCollectionDTO();
            $CategorySeoDTO->setLocal($locale);
            $this->addSeo($CategorySeoDTO);
        }

        return $this->seo;
    }

    public function addSeo(CategoryProductSeoCollectionDTO $seo): void
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

    public function removeSeo(CategoryProductSeoCollectionDTO $seo): void
    {
        $this->seo->removeElement($seo);
    }


    /** Секции свойств продукта категории */

    public function getSection(): ArrayCollection
    {
        return $this->section;
    }

    public function addSection(CategoryProductSectionCollectionDTO $section): void
    {
        if(!$this->section->contains($section))
        {
            $this->section->add($section);
        }
    }

    public function removeSection(CategoryProductSectionCollectionDTO $section): void
    {
        $this->section->removeElement($section);
    }


    /** Торговые предложения */

    public function getOffer(): ?CategoryProductOffersDTO
    {
        return $this->offer;
    }

    public function setOffer(?CategoryProductOffersDTO $offer): void
    {
        $this->offer = $offer;
    }

    public function resetOffer(): void
    {
        $this->offer->resetVariation();

        if($this->offer->isOffer() === false)
        {
            $this->offer = null;
        }
    }


    /**  Модификатор события  */

    public function getModify(): Modify\CategoryProductModifyDTO
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

    public function getCover(): ?CategoryProductCoverDTO
    {
        return $this->cover;
    }

    public function setCover(?CategoryProductCoverDTO $cover): void
    {
        $this->cover = $cover;
    }

    /**
     * Domain
     */
    public function getDomain(): ArrayCollection
    {
        return $this->domain;
    }

    public function setDomain(ArrayCollection $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function addDomain(Domains\CategoryProductDomainDTO $domain): self
    {
        $filter = $this->domain->filter(function(Domains\CategoryProductDomainDTO $element) use ($domain) {
            return $domain->getDomain() === $element->getDomain();
        });

        if($filter->isEmpty())
        {
            $this->domain->add($domain);
        }

        return $this;
    }

}
