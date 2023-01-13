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

namespace App\Module\Products\Category\UseCase\Admin\NewEdit\Section\Fields;



use App\Module\Products\Category\Entity\Section\Field\FieldInterface;
use App\Module\Products\Category\Entity\Section\Field\Trans\TransInterface;
use App\Module\Products\Category\Type\Section\Field\Id\FieldUid;
use App\Module\Products\Category\UseCase\Admin\NewEdit\Section\Fields\Trans\SectionFieldTransDTO;
use App\System\Type\Field\InputField;
use App\System\Type\Locale\Locale;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** Поля свойств продукта в секции */
final class SectionFieldCollectionDTO implements FieldInterface
{
    private ?FieldUid $id = null;
    
    /** Сортировка поля в секции
     * @var int
     */
    #[Assert\Range(min: 0, max: 999)]
    private int $sort = 100;
    
    /** Тип поля (input, select, textarea ....) */
    #[Assert\NotBlank]
    private InputField $type;
    
    /** Публичное свойство
     * @var bool
     */
    private bool $public = true;
    
    /** Обязательное к заполнению
     * @var bool
     */
    private bool $required = true;
    
    #[Assert\Valid]
    private ArrayCollection $trans;
    

    public function __construct() { $this->trans = new  ArrayCollection(); }
    
    /**
     * @return FieldUid|null
     */
    public function getEquals() : ?FieldUid
    {
        return $this->id;
    }
    
    /**
     * @param FieldUid $id
     */
    public function setId(FieldUid $id) : void
    {
        $this->id = $id;
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
     * @return InputField
     */
    public function getType() : InputField
    {
        return $this->type;
    }
    
    /**
     * @param InputField $type
     */
    public function setType(InputField $type) : void
    {
        $this->type = $type;
    }
    
    /**
     * @return bool
     */
    public function isPublic() : bool
    {
        return $this->public;
    }
    
    /**
     * @param bool $public
     */
    public function setPublic(bool $public) : void
    {
        $this->public = $public;
    }
    
    /**
     * @return bool
     */
    public function isRequired() : bool
    {
        return $this->required;
    }
    
    /**
     * @param bool $required
     */
    public function setRequired(bool $required) : void
    {
        $this->required = $required;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getTrans() : ArrayCollection
    {
        /* Вычисляем расхождение и добавляем неопределенные локали */
        foreach(Locale::diffLocale($this->trans) as $locale)
        {
            $SectionFieldTransDTO = new SectionFieldTransDTO();
            $SectionFieldTransDTO->setLocal($locale);
            $this->addTran($SectionFieldTransDTO);
        }
    
        return $this->trans;
    }
    
    
    
    /**
     * @param SectionFieldTransDTO $trans
     * @return void
     */
    public function addTran(SectionFieldTransDTO $trans) : void
    {
        
        if(!$this->trans->contains($trans))
        {
            $this->trans[] = $trans;
        }
    }
    
    public function removeTran(SectionFieldTransDTO $trans) : void
    {
        $this->trans->removeElement($trans);
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getTranClass() : TransInterface
    {
        return new SectionFieldTransDTO();
    }
    
}

