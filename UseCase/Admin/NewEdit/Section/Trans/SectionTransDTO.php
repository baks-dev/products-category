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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Trans;

use BaksDev\Products\Category\Entity\Section\ProductCategorySection;
use BaksDev\Products\Category\Entity\Section\Trans\ProductCategorySectionTransInterface;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Type\Section\Id\ProductCategorySectionUid;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

final class SectionTransDTO implements ProductCategorySectionTransInterface
{
	#[Assert\Uuid]
	private ?ProductCategorySectionUid $section = null;
	
	/** Локаль */
	#[Assert\NotBlank]
	private ?Locale $local;
	
	/** Название секции (строка с точкой, нижнее подчеркивание тире процент скобки) */
	#[Assert\NotBlank]
	#[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
	private ?string $name = null;
	
	/** Краткое описание (строка с точкой, нижнее подчеркивание тире процент скобки) */
	#[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
	private ?string $description = null;
	
	/** Локаль */
	
	public function getLocal() : ?Locale
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
	
	
	/** Название секции */
	
	public function setName(?string $name) : void
	{
		$this->name = $name;
	}
	
	public function getName() : ?string
	{
		return $this->name;
	}
	
	/** Краткое описание */
	
	public function getDescription() : ?string
	{
		return $this->description;
	}
	
	public function setDescription(?string $description) : void
	{
		$this->description = $description;
	}

    public function setSection(ProductCategorySection|ProductCategorySectionUid $section): self
    {
        $this->section = $section instanceof ProductCategorySection ? $section->getId() : $section;
        return $this;
    }
	
	

	
	
	//    public function __construct(Locale $locale = null, string $name = null, string $description = null)
	//    {
	//        $this->local = $locale;
	//        $this->name = $name;
	//        $this->description = $description;
	//    }
	//
	//    /**
	//     * @param string|Locale $local
	//     */
	//    public function setLocal(string|Locale $local) : void
	//    {
	//        $this->local = $local instanceof Locale ? $local : new Locale($local) ;
	//    }
	//
	//    public function getLocal() : Locale
	//    {
	//        return $this->local;
	//    }
	//
	//    public function getName(): string
	//    {
	//        return $this->name;
	//    }
	//
	//    public function getDesc() : ?string
	//    {
	//        return $this->desc;
	//    }
	
}

