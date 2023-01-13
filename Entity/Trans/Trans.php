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

namespace App\Module\Products\Category\Entity\Trans;



use App\Module\Products\Category\Entity\Event\Event;
use App\System\Services\EntityEvent\EntityEvent;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Category */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_trans')]
class Trans extends EntityEvent
{
    public const TABLE = 'product_category_trans';
    
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: "trans")]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    protected Event $event;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column( type: Types::STRING, length: 100, nullable: false)]
    protected string $name;
    
    /** Описание */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description = null;
    
    public function __construct(Event $event) {
        $this->event = $event;
    }
    
    
    public function name(Locale $locale) : ?string
    {
        if($this->local->getValue() === $locale->getValue())
        {
            return $this->name;
        }
        
        return null;
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
            return  ($this->event->getId() === $dto->getEquals() &&
               $dto->getLocal()->getValue() === $this->local->getValue());
            
        }
    
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    //    public function updCategoryTrans(TransInterface $categoryTrans) : void
//    {
////        if(property_exists($categoryTrans, 'name'))
////        {
////            $this->name = $categoryTrans->name;
////        }
////
////        if(property_exists($categoryTrans, 'description'))
////        {
////            $this->description = $categoryTrans->description;
////        }
//    }
    
//    public function getCategoryTrans(TransInterface $categoryTrans) : TransInterface
//    {
//        $oReflectionClass = new \ReflectionClass($categoryTrans);
//
//        foreach($oReflectionClass->getProperties() as $property)
//        {
//            $propertyName = $property->getName();
//            $propertyNameSetter =  'set'.ucfirst($propertyName);
//
//            if(property_exists($this, $propertyName) && method_exists($categoryTrans, $propertyNameSetter))
//            {
//                $categoryTrans->$propertyNameSetter($this->{$propertyName});
//            }
//        }
//
//        return $categoryTrans;
//    }
//
}
