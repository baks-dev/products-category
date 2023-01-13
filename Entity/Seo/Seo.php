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

namespace App\Module\Products\Category\Entity\Seo;

use App\Module\Products\Category\Entity\Event\Event;
use App\System\Services\EntityEvent\EntityEvent;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'product_category_seo')]
class Seo extends EntityEvent
{
    public const TABLE = "product_category_seo";
    
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Event::class, cascade: ["remove", "persist"], inversedBy: "seo")]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    protected Event $event;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
    protected Locale $local;
    
    /** Шаблон META TITLE */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $title;
    
    /** Шаблон META KEYWORDS */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $keywords;
    
    /** Шаблон META DESCRIPTION */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description;
    
    
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
        if($dto instanceof SeoInterface)
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
        if($dto instanceof SeoInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    public function equals($dto) : bool
    {
        if($dto instanceof SeoInterface)
        {
            return  ($this->event->getId() === $dto->getEquals() &&
              $dto->getLocal()->getValue() === $this->local->getValue());
            
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    //    public function updCategorySeo(SeoInterface $seo) : void
    //    {
    ////        if(property_exists($seo, 'title'))
    ////        {
    ////            $this->title = $seo->title;
    ////        }
    ////
    ////        if(property_exists($seo, 'keywords'))
    ////        {
    ////            $this->keywords = $seo->keywords;
    ////        }
    ////
    ////        if(property_exists($seo, 'description'))
    ////        {
    ////            $this->description = $seo->description;
    ////        }
    //    }
    
    //    public function getCategorySeo(SeoInterface $seo) : SeoInterface
    //    {
    //        $oReflectionClass = new \ReflectionClass($seo);
    //
    //        foreach($oReflectionClass->getProperties() as $property)
    //        {
    //            $propertyName = $property->getName();
    //            $propertyNameSetter =  'set'.ucfirst($propertyName);
    //
    //            if(property_exists($this, $propertyName) && method_exists($seo, $propertyNameSetter))
    //            {
    //                $seo->$propertyNameSetter($this->{$propertyName});
    //            }
    //        }
    //
    //        return $seo;
    //    }
    
}
