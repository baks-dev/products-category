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

namespace BaksDev\Products\Category\Repository\CategoryParentForm;


use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryParentFormDTO
{
    public $event;
    public $category;
    public $name;
    public $parent;
    
    
    public function __construct(
      $event,
      $category,
      $name,
      $parent,
    
    ) {
        $this->category = new ParentCategoryUid($category);
        $this->name = $name;
        $this->parent = $parent;
        $this->event = $event;
    }
    
}

