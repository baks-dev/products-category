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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Seo;

use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Entity\Seo\CategoryProductSeoInterface;
use BaksDev\Products\Category\Type\Event\CategoryProductEventUid;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryProductSeoCollectionDTO implements CategoryProductSeoInterface
{
    #[Assert\Uuid]
    private ?CategoryProductEventUid $event = null;

    /** Локаль */
    #[Assert\NotBlank]
    private readonly Locale $local;

    /** Шаблон META TITLE (строка с точкой, запятой, нижнее подчеркивание тире процент скобки) */
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $title = null;

    /** Шаблон META DESCRIPTION (строка с точкой, запятой, нижнее подчеркивание тире процент скобки) */
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $description = null;

    /** Шаблон META KEYWORDS (строка с точкой, запятой, нижнее подчеркивание тире процент скобки) */
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $keywords = null;


    public function withEvent(CategoryProductEvent|CategoryProductEventUid $event): void
    {
        $this->event = $event instanceof CategoryProductEvent ? $event->getId() : $event;
    }


    /** Локаль */

    public function getLocal(): Locale
    {
        return $this->local;
    }

    /** Локаль */

    public function setLocal(Locale $local): void
    {
        if(!(new ReflectionProperty(self::class, 'local'))->isInitialized($this))
        {
            $this->local = $local;
        }
    }

    /** Шаблон META TITLE */

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /** Шаблон META KEYWORDS */

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /** Шаблон META DESCRIPTION */

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


















    //
    //    /**
    //     * @return Locale
    //     */
    //    public function getLocal() : Locale
    //    {
    //        return $this->local;
    //    }
    //
    //    /**
    //     * @return string
    //     */
    //    public function getTitle(): string
    //    {
    //        return $this->title;
    //    }
    //
    //    /**
    //     * @return string
    //     */
    //    public function getKeywords(): string
    //    {
    //        return $this->keywords;
    //    }
    //
    //    /**
    //     * @return string
    //     */
    //    public function getDescription(): string
    //    {
    //        return $this->description;
    //    }
    //

}
