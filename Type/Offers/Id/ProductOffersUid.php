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

namespace BaksDev\Products\Category\Type\Offers\Id;

use BaksDev\Core\Type\UidType\Uid;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

final class ProductOffersUid extends Uid
{
    public const TYPE = 'product_category_offers_id';
	
    private bool $multiple;
	
	private ?string $option;
	
	public function __construct(AbstractUid|string|null $value = null, string $option = null, bool $multiple = false)
	{
		parent::__construct($value);
		
		$this->multiple = $multiple;
		$this->option = $option;
	}
	
	public function getOption() : ?string
	{
		return $this->option;
	}
	
    public function isMultiple() : bool
    {
        return $this->multiple;
    }

}