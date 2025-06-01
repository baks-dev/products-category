<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

declare(strict_types=1);

namespace BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryResult;

use ArrayObject;
use Symfony\Component\Validator\Constraints as Assert;

/** @see MenuPublicCategoryResult */
final class MenuPublicCategoryResult extends ArrayObject
{
    public function __construct(object|array $array = [])
    {
        foreach($array as $category)
        {
            $this->offsetSet($category['id'], new MenuPublicCategoryDTO(...$category));

            /**
             * Добавляем в родительскую категорию идентификатор потомка
             */
            if(isset($category['parent']) && $this->offsetExists($category['parent']))
            {
                /** @var MenuPublicCategoryDTO $parent */
                $parent = $this->offsetGet($category['parent']);
                $parent->addChildValue($category['id']);
            }
        }

        parent::__construct();
    }
}