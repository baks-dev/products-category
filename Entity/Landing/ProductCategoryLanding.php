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

namespace BaksDev\Products\Category\Entity\Landing;


use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Core\Services\EntityEvent\EntityEvent;
use BaksDev\Core\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Посадочные блоки категории */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_landing')]
class ProductCategoryLanding extends EntityEvent
{
    public const TABLE = 'product_category_landing';
    
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Event::class, cascade: ["remove", "persist"], inversedBy: "landings")]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    protected Event $event;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
    protected Locale $local;
    
    /** Верхний блок */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $header;
    
    /** Нижний блок */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $bottom;
    
    public function __construct(Event $event)
    {
        $this->event = $event;
    }
    
    
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof LandingInterface)
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

        if($dto->getHeader() === null && $dto->getBottom() === null)
        {
            return false;
        }
        
        if($dto instanceof LandingInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    public function equals($dto) : bool
    {
        if($dto instanceof LandingInterface)
        {
            return  ($this->event->getId() === $dto->getEquals() &&
              $this->local->getValue() === $dto->getLocal()->getValue());
            
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
//    public function updCategoryLanding(LandingInterface $landing) : void
//    {
//
////        if(property_exists($landing, 'header'))
////        {
////            $this->header = $landing->header;
////        }
////
////        if(property_exists($landing, 'bottom'))
////        {
////            $this->bottom = $landing->bottom;
////        }
//    }
    
//    public function getCategoryLanding(LandingInterface $landing) : LandingInterface
//    {
//        $oReflectionClass = new \ReflectionClass($landing);
//
//        foreach($oReflectionClass->getProperties() as $property)
//        {
//            $propertyName = $property->getName();
//            $propertyNameSetter =  'set'.ucfirst($propertyName);
//
//            if(property_exists($this, $propertyName) && method_exists($landing, $propertyNameSetter))
//            {
//                $landing->$propertyNameSetter($this->{$propertyName});
//            }
//        }
//
//        return $landing;
//    }
    
    
    //    /**
//     * @param Event|CategoryEvent $event
//     */
//    public function __construct(Event|CategoryEvent $event, Locale $local)
//    {
//        $this->setEvent($event);
//        $this->local = $local;
//    }
//
//    public function clone(Event|CategoryEvent $event) : self
//    {
//        $clone = clone $this;
//        $clone->setEvent($event);
//        return $clone;
//    }
//
//    /**
//     * @param Event|CategoryEvent $event
//     */
//    private function setEvent(Event|CategoryEvent $event) : void
//    {
//        $this->event = $event instanceof Event ? $event->getId() : $event;;
//    }
//
//    /**
//     * @return Locale
//     */
//    public function getLocal() : Locale
//    {
//        return $this->local;
//    }
    

    
    
    //    /**
//     * @param Locale $local
//     * @param string $header
//     * @param string|null $bottom
//     */
//    public function addLanding(Locale $local, ?string $header, ?string $bottom)
//    {
//        $this->local = $local;
//        $this->header = $header;
//        $this->bottom = $bottom;
//    }
//
//    /**
//     * @return Locale
//     */
//    public function getLocal() : Locale
//    {
//        return $this->local;
//    }

    
}
