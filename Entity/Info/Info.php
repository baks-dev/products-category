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

namespace App\Module\Products\Category\Entity\Info;

use App\Module\Products\Category\Entity\Event\Event;
use App\System\Services\EntityEvent\EntityEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Неизменяемые данные Категории */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_info')]
#[ORM\Index(columns: ['active'])]
#[ORM\Index(columns: ['url'])]
class Info extends EntityEvent
{
    public const TABLE = 'product_category_info';
    
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'info', targetEntity: Event::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    protected ?Event $event;
    
    /** Семантическая ссылка на раздел */
    #[ORM\Column(name: 'url', type: Types::STRING, nullable: false)]
    protected string $url;
    
    /** Статус активности раздела */
    #[ORM\Column(name: 'active', type: Types::BOOLEAN, nullable: false)]
    protected bool $active = true;
    
    /** Количество товаров в разделе */
    #[ORM\Column(name: 'counter', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    protected int $counter = 0;
    
    /**
     * @param Event $event
     */
    public function __construct(Event $event) { $this->event = $event; }
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof InfoInterface)
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
        if($dto instanceof InfoInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    
    
    
    //use EntityEvent;
    
    //    public function updInfo(InfoInterface $info)
    //    {
    ////        if(property_exists($info, 'url'))
    ////        {
    ////            $this->url = $info->url;
    ////        }
    ////
    ////        if(property_exists($info, 'active'))
    ////        {
    ////            $this->active = $info->active;
    ////        }
    //    }
    
    //    public function getInfo(InfoInterface $info) : InfoInterface
    //    {
    //        $oReflectionClass = new \ReflectionClass($info);
    //
    //        foreach($oReflectionClass->getProperties() as $property)
    //        {
    //            $propertyName = $property->getName();
    //            $propertyNameSetter =  'set'.ucfirst($propertyName);
    //
    //            if(property_exists($this, $propertyName) && method_exists($info, $propertyNameSetter))
    //            {
    //                $info->$propertyNameSetter($this->{$propertyName});
    //            }
    //        }
    //
    //        return $info;
    //
    //    }
    
    //    /**
    //     * @return string
    //     */
    //    public function getUrl() : string
    //    {
    //        return $this->url;
    //    }
    //
    //    /**
    //     * @return bool
    //     */
    //    public function isActive() : bool
    //    {
    //        return $this->active;
    //    }
    
    //    /**
    //     * @param CategoryUid $category
    //     * @param string $url
    //     * @param bool $active
    //     * @param int $counter
    //     */
    //    public function __construct(Event|CategoryEvent $event)
    //    {
    //        $this->setEvent($event);
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
    //    protected function setEvent(Event|CategoryEvent $event) : void
    //    {
    //        $this->event = $event instanceof Event ? $event->getId() : $event;
    //    }
    //
    
    //
    //
    //    public function addCounter()
    //    {
    //        $this->counter = $this->counter + 1;
    //    }
    //
    //    public function subCounter()
    //    {
    //        if($this->counter !== 0)
    //        {
    //            $this->counter = $this->counter - 1;
    //        }
    //    }
    
}
