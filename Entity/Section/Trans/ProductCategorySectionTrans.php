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

namespace BaksDev\Products\Category\Entity\Section\Trans;


use BaksDev\Products\Category\Entity\Section\Section;
use BaksDev\Core\Services\EntityEvent\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Section */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_section_trans')]
class Trans extends EntityEvent
{
    public const TABLE = 'product_category_section_trans';
    
    /** Связь на секцию */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Section::class, cascade: ["all"], inversedBy: "trans")]
    #[ORM\JoinColumn(name: 'section_id', referencedColumnName: 'id')]
    protected Section $section;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    protected string $name;
    
    /** Описание */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description;
    
    
    public function __construct(Section $section) {

        $this->section = $section;
    }
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof TransInterface)
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
        if($dto instanceof TransInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function equals($dto) : bool
    {
        if($dto instanceof TransInterface)
        {
            return  ($this->section->getId() === $dto->getEquals() &&
              $dto->getLocal()?->getValue() === $this->local->getValue());
            
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
//    /**
//     * @param Section|null $section
//     * @param Locale $local
//     */
//    public function __construct(Section $section, Locale $local)
//    {
//        $this->section = $section;
//        $this->local = $local;
//    }
    
    
//    public function updSectionTrans(TransInterface $sectionTrans) : void
//    {
//        if(property_exists($sectionTrans, 'name'))
//        {
//            $this->name = $sectionTrans->name;
//        }
//
//        if(property_exists($sectionTrans, 'description'))
//        {
//            $this->description = $sectionTrans->description;
//        }
//    }
    
    //    /**
//     * @param SectionUid $section
//     */
//    public function __construct(Section|SectionUid $section) {
//
//        $this->section = $section instanceof Section ? $section->getId() : $section;
//    }
//
//    /**
//     * @param Locale $local
//     * @param string $name
//     * @param string|null $desc
//     */
//    public function addSectionTrans(Locale $local, string $name, ?string $desc)
//    {
//        $this->local = $local;
//        $this->name = $name;
//        $this->description = $desc;
//    }

    
}
