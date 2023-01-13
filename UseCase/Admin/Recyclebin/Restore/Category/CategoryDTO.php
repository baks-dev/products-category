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

namespace App\Module\Products\Category\UseCase\Admin\Recyclebin\Restore\Category;

use App\Module\Products\Category\Entity\Event\EventInterface;
use App\Module\Products\Category\Type\Event\CategoryEvent;
use App\Module\Products\Category\UseCase\Admin\Recyclebin\Restore\Modify\ModifyDTO;

final class CategoryDTO implements EventInterface
{
    /**
     * Идентификатор события
     * @var CategoryEvent|null
     */
    private ?CategoryEvent $id = null;

    private ModifyDTO $modify;
    
    public function __construct() {
        $this->modify = new ModifyDTO();
    }
    

    /**
     * @return CategoryEvent|null
     */
    public function getEvent() : ?CategoryEvent
    {
        return $this->id;
    }
    
    /**
     * @param CategoryEvent $id
     */
    public function setId(CategoryEvent $id) : void
    {
        $this->id = $id;
    }
    
    
    /* Modify  */
    
    
    /**
     * @return ModifyDTO
     */
    public function getModify() : ModifyDTO
    {
        return $this->modify;
    }
    
    /** Метод для инициализации и маппинга сущности на DTO в коллекции  */
    public function getModifyClass() : ModifyDTO
    {
        return new ModifyDTO();
    }

}

