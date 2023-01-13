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

namespace App\Module\Products\Category\Repository\CategoryOffersForm;

use App\Module\Products\Category\Type\Offers\Id\OffersUid;

final class CategoryOffersFormDTO
{
    public OffersUid $id;
    
    public string $name;
    public ?string $reference;
    public bool $image;
    public bool $price;
    public bool $quantitative;
    public bool $multiple;
    public bool $article;
    
    public function __construct(
      OffersUid $id,
      ?string $reference,
      bool $image,
      bool $price,
      bool $quantitative,
      bool $article,
      bool $multiple,
      string $name,
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->reference = $reference;
        $this->image = $image;
        $this->price = $price;
        $this->multiple = $multiple;
        $this->article = $article;
        $this->quantitative = $quantitative;
    }
    
}