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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers;


use BaksDev\Products\Category\Entity\Offers\OffersInterface;
use BaksDev\Products\Category\Entity\Offers\Trans\TransInterface;
use BaksDev\Products\Category\Type\Offers\Id\OffersUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Trans\OffersTransDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

final class OffersCollectionDTO implements OffersInterface
{
    private ?OffersUid $id = null;
    
    /**  Сортировка */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 100;
    
    /** Справочник
     * @var string|null
     */
    private ?string $reference = null;
    
    /** Загрузка пользовательских изображений
     * @var bool
     */
    private bool $image = false;
    
    /** Торговое предложение с ценой
     * @var bool
     */
    private bool $price = false;
    
    
    /** Количественный учет */
    private bool $quantitative = false;
    
    /** Торговое предложение с артикулом */
    protected bool $article = false;
    
    /** Множественный выбор */
    private bool $multiple = false;
    
    /** Настройки локали торгового предложения */
    #[Assert\Valid]
    private ArrayCollection $trans;
    
    
    public function __construct() { $this->trans = new ArrayCollection(); }
    
    /* SORT */
    
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
    
    /* REFERENCE */
    
    /**
     * @return string|null
     */
    public function getReference() : ?string
    {
        return $this->reference;
    }
    
    /**
     * @param string|null $reference
     */
    public function setReference(?string $reference) : void
    {
        $this->reference = $reference;
    }
    
    /* IMAGE */
    
    /**
     * @return bool
     */
    public function isImage() : bool
    {
        return $this->image;
    }
    
    /**
     * @param bool $image
     */
    public function setImage(bool $image) : void
    {
        $this->image = $image;
    }
    
    /* PRICE */
    
    /**
     * @return bool
     */
    public function isPrice() : bool
    {
        return $this->price;
    }
    
    /**
     * @param bool $price
     */
    public function setPrice(bool $price) : void
    {
        $this->price = $price;
    }
    
    /* MULTIPLE */
    
    /**
     * @return bool
     */
    public function isMultiple() : bool
    {
        return $this->multiple;
    }
    
    /**
     * @param bool $multiple
     */
    public function setMultiple(bool $multiple) : void
    {
        $this->multiple = $multiple;
    }
    
    /* article */
    
    /**
     * @return bool
     */
    public function isArticle() : bool
    {
        return $this->article;
    }
    
    /**
     * @param bool $article
     */
    public function setArticle(bool $article) : void
    {
        $this->article = $article;
    }
    
    /* TRANS */
    
    /**
     * @return ArrayCollection|Collection
     */
    public function getTrans() : ArrayCollection|Collection
    {
        return $this->trans;
    }
    
    /** Добавляем перевод категории
     * @param OffersTransDTO $trans
     * @return void
     */
    public function addTran(OffersTransDTO $trans) : void
    {

        if(!$this->trans->contains($trans))
        {
            $this->trans[] = $trans;
        }
    }
    
    public function removeTran(OffersTransDTO $trans) : void
    {
        $this->trans->removeElement($trans);
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getTranClass() : TransInterface
    {
        return new OffersTransDTO();
    }
    
    /**
     * @return OffersUid|null
     */
    public function getEquals() : ?OffersUid
    {
        return $this->id;
    }
    
    /**
     * @param OffersUid $id
     */
    public function setId(OffersUid $id) : void
    {
        $this->id = $id;
    }
    
    /**
     * @return bool
     */
    public function isQuantitative() : bool
    {
        return $this->quantitative;
    }
    
    /**
     * @param bool $quantitative
     */
    public function setQuantitative(bool $quantitative) : void
    {
        $this->quantitative = $quantitative;
    }
    
}

