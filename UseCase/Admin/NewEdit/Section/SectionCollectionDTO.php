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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Section;


use BaksDev\Products\Category\Entity\Section\SectionInterface;
use BaksDev\Products\Category\Type\Section\Id\SectionUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields\SectionFieldCollectionDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Trans\SectionTransDTO;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** Секции для свойств продукта */
final class SectionCollectionDTO implements SectionInterface
{
    private ?SectionUid $id = null;
    
    /** Сортировка секции свойств продукта категории
     * @var int
     */
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 100;
    
    /** Настройки локали секции */
    #[Assert\Valid]
    private ArrayCollection $trans;
    
    /** Коллекция свойств продукта в секции */
    #[Assert\Valid]
    private ArrayCollection $fields;
    
    public function __construct()
    {
        $this->trans = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }
    
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
    
    /**
     * @return ArrayCollection
     */
//    public function getTrans() : ArrayCollection
//    {
//
//
//
//
//        return $this->trans;
//    }
    
    /**
     * @return ArrayCollection
     */
    public function getTrans() : ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->trans) as $locale)
        {
            $SectionTransDTO = new SectionTransDTO();
            $SectionTransDTO->setLocal($locale);
            $this->addTran($SectionTransDTO);
        }
        
        return $this->trans;
    }
    
    
    /** Добавляем перевод категории
     * @param SectionTransDTO $trans
     * @return void
     */
    public function addTran(SectionTransDTO $trans) : void
    {
        
        if(!$this->trans->contains($trans))
        {
            $this->trans[] = $trans;
        }
    }
    
    public function removeTran(SectionTransDTO $trans) : void
    {
        $this->trans->removeElement($trans);
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getTranClass() : SectionTransDTO
    {
        return new SectionTransDTO();
    }
    
    
    
    
    
    /**
     * @return ArrayCollection
     */
    public function getFields() : ArrayCollection
    {
        return $this->fields;
    }

    public function addField(SectionFieldCollectionDTO $field) : void
    {
        if(!$this->fields->contains($field))
        {
            $this->fields[] = $field;
        }
    }
    
    public function removeField(SectionFieldCollectionDTO $field) : void
    {
        $this->fields->removeElement($field);
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getFieldClass() : SectionFieldCollectionDTO
    {
        return new SectionFieldCollectionDTO();
    }
    
    
    
    /**
     * @return SectionUid|null
     */
    public function getEquals() : ?SectionUid
    {
        return $this->id;
    }
    
    /**
     * @param SectionUid $id
     */
    public function setId(SectionUid $id) : void
    {
        $this->id = $id;
    }
    
}