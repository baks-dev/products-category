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

namespace App\Module\Products\Category\Type\Offers\Id;

use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

final class OffersUid
{
    public const TYPE = 'product_category_offers_id';
    
    private Uuid $value;
    /**
     * @var mixed|null
     */
    private ?string $name;
    
    
    private bool $multiple;
    
    public function __construct(AbstractUid|string|null $value = null, string $name = null, $multiple = false)
    {
        if($value === null)
        {
            $value = Uuid::v4();
        }
        
        else if(is_string($value))
        {
            $value = new Uuid($value);
        }
        
        $this->value = $value;
        
        $this->name = $name;
        $this->multiple = $multiple;
    }
    
    public function __toString() : string
    {
        return $this->value;
    }
    
    public function getValue() : AbstractUid
    {
        return $this->value;
    }
    
 
    public function getName() : ?string
    {
        return $this->name;
    }
    
    /**
     * @return bool
     */
    public function isMultiple() : bool
    {
        return $this->multiple;
    }

}