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

namespace App\Module\Products\Category\Entity\Section\Field;

use App\Module\Products\Category\Entity\Section\Section;
use App\Module\Products\Category\Type\Section\Field\Id\FieldUid;
use App\Module\Products\Category\Type\Section\Id\SectionUid;
use App\System\Services\EntityEvent\EntityEvent;
use App\System\Type\Field\InputField;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Поля секции */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_section_field')]
class Field extends EntityEvent
{
    public const TABLE = 'product_category_section_field';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: FieldUid::TYPE)]
    protected FieldUid $id;
    
    /** Связь на секцию */
    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: "fields")]
    #[ORM\JoinColumn(name: 'section_id', referencedColumnName: 'id', nullable: true)]
    protected ?Section $section;
    
    /** Перевод */
    #[ORM\OneToMany(mappedBy: 'field', targetEntity: Trans\Trans::class, cascade: ['all'], orphanRemoval: true)]
    protected Collection $trans;
    
    /** Тип поля (input, select, textarea ....)  */
    #[ORM\Column(name: 'type', type: InputField::TYPE, length: 10, nullable: false, options: ['default' => 'input'])]
    protected InputField $type;
    
    /** Публичное свойство */
    #[ORM\Column(name: 'public', type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    protected bool $public = true;
    
    /** Обязательное к заполнению */
    #[ORM\Column(name: 'required', type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    protected bool $required = true;
    
    /** Сортировка */
    #[ORM\Column(name: 'sort', type: Types::SMALLINT, length: 3, nullable: false, options: ['default' => 100])]
    protected int $sort = 100;
    
    public function __construct(Section $section)
    {
        $this->id = new FieldUid();
        $this->section = $section;
        
        $this->trans = new ArrayCollection();
    }
    
//    public function __clone()
//    {
//        $this->id = new FieldUid();
//    }
	
	public function __toString() : string
	{
		return $this->id;
	}
	
    /**
     * @return FieldUid
     */
    public function getId() : FieldUid
    {
        return $this->id;
    }

    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof FieldInterface)
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
        if($dto instanceof FieldInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    public function removeElement() : void
    {
        $this->section = null;
    }

    
    public function equals($dto) : bool
    {
        if($dto instanceof FieldInterface)
        {
            return $this->id === $dto->getEquals();
        }
    
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    
    
//    public function updSectionField(FieldInterface $field) : void
//    {
//        if(property_exists($field, 'trans'))
//        {
//            foreach($field->trans as $trans)
//            {
//                $categorySectionTrans = new Field\Trans($this, $trans->local);
//                $categorySectionTrans->updFieldTrans($trans);
//                $this->addTrans($categorySectionTrans);
//            }
//        }
//
//        if(property_exists($field, 'type'))
//        {
//            $this->type = $field->type;
//        }
//
//        if(property_exists($field, 'public'))
//        {
//            $this->public = $field->public;
//        }
//
//        if(property_exists($field, 'required'))
//        {
//            $this->required = $field->required;
//        }
//
//        if(property_exists($field, 'sort'))
//        {
//            $this->sort = $field->sort;
//        }
//    }
//
//    /** Добавляем перевод поля
//     * @param Section\Trans $trans
//     * @return void
//     */
//    public function addTrans(Field\Trans $trans) : void
//    {
//        if(!$this->trans->contains($trans))
//        {
//            $this->trans[] = $trans;
//        }
//    }
//
    //
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
    //            $this->addTrans(new Field\Trans($this, $locale));
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
    //    public function addTrans(Field\Trans $trans) : void
    //    {
    //        if(!$this->trans->contains($trans))
    //        {
    //            $this->trans[] = $trans;
    //        }
    //    }
    //
    //
    
    /**
     * @param SectionUid $section
     */
    /**
     * @param SectionUid $section
     */
    //    public function __construct(Section|SectionUid $section) {
    //        $this->id = new FieldUid();
    //        $this->section = $section instanceof Section ? $section->getId() : $section;
    //    }
    
    //    /**
    //     * @param int $sort
    //     * @param InputField $type
    //     * @param bool $public
    //     * @param bool $required
    //     */
    //    public function addSectionField(InputField $type, int $sort = 100, bool $public = true, bool $required = true)
    //    {
    //        $this->sort = $sort;
    //        $this->type = $type;
    //        $this->public = $public;
    //        $this->required = $required;
    //    }
    //
    //    /**
    //     * @return FieldUid
    //     */
    //    public function getId() : FieldUid
    //    {
    //        return $this->id;
    //    }
    
}