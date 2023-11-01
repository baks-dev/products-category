<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Entity\Landing\ProductCategoryLandingInterface;
use BaksDev\Products\Category\Type\Event\ProductCategoryEventUid;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

/** @see ProductCategoryLanding */
final class LandingCollectionDTO implements ProductCategoryLandingInterface
{
	#[Assert\Uuid]
	private ?ProductCategoryEventUid $event = null;
	
	/** Локаль */
	#[Assert\NotBlank]
	private readonly Locale $local;
	
	/** Верхний посадочный блок */
	private ?string $header = null;
	
	/** Нижний посадочный блок */
	private ?string $bottom = null;


    public function setEvent(ProductCategoryEvent|ProductCategoryEventUid $event): void
    {
        $this->event = $event instanceof ProductCategoryEvent ? $event->getId() : $event;
    }


	/** Локаль */
	
	public function getLocal() : Locale
	{
		return $this->local;
	}

	
	/** Локаль */
	
	public function setLocal(Locale $local) : void
	{
		if(!(new ReflectionProperty(self::class, 'local'))->isInitialized($this))
		{
			$this->local = $local;
		}
	}
	
	/** Верхний посадочный блок */
	
	public function getHeader() : ?string
	{
		return $this->header;
	}
	
	
	public function setHeader(?string $header) : void
	{
		$this->header = $header;
	}
	
	/** Нижний посадочный блок */
	
	
	public function getBottom() : ?string
	{
		return $this->bottom;
	}
	
	public function setBottom(?string $bottom) : void
	{
		$this->bottom = $bottom;
	}
	
}

