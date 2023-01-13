<?php

/*
*  Copyright Baks.dev <admin@baks.dev>
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

namespace App\Module\Products\Category\Entity;

use App\Module\Products\Category\Entity\Event\Event;
use App\Module\Products\Category\Type\Event\CategoryEvent;
use App\Module\Products\Category\Type\Id\CategoryUid;
use Doctrine\ORM\Mapping as ORM;

/* Категории продуктов */

#[ORM\Entity()]
#[ORM\Table(name: 'product_category')]
class Category
{
    
    public const TABLE = 'product_category';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategoryUid::TYPE)]
    private CategoryUid $id;
    
    /** ID События */
    #[ORM\Column(type: CategoryEvent::TYPE, unique: true, nullable: false)]
    private ?CategoryEvent $event = null;
    

    public function __construct() { $this->id = new CategoryUid(); }
    
    /**
     * @return CategoryUid
     */
    public function getId() : CategoryUid
    {
        return $this->id;
    }
    
    /**
     * @param CategoryUid $id
     */
    public function setId(CategoryUid $id) : void
    {
        $this->id = $id;
    }
    
    /**
     * @return CategoryEvent|null
     */
    public function getEvent() : ?CategoryEvent
    {
        return $this->event;
    }
    

    public function setEvent(Event|CategoryEvent $event) : void
    {
        $this->event = $event instanceof Event ? $event->getId() : $event;
    }
    
}