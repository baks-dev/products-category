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
use Generator;
use Symfony\Component\Validator\Constraints as Assert;

/** @see MenuPublicCategory */
final class MenuPublicCategoryDTO
{
    private ArrayObject $child;

    private Generator $products;

    public function __construct(
        private readonly mixed $id, // " => "01876af0-ddfc-70c3-ab25-5f85f55a9907"
        private readonly mixed $event, // " => "01876af0-ddfb-7a6b-8ebe-14c48a15d8dc"
        private readonly mixed $sort, // " => 500
        private readonly mixed $parent, // " => null
        private readonly mixed $groups, // " => "01876af0-ddfb-7a6b-8ebe-14c48a15d8dc"

        private readonly string $category_url, // " => "triangle"
        private readonly string $category_name, // " => "Triangle"

        private readonly ?string $category_cover_image, // " => "/upload/product_category_cover/64340ccbdedae"
        private readonly ?bool $category_cover_cdn, // " => true
        private readonly ?string $category_cover_ext, // " => "webp"

        private readonly int $level, // " => 1

    )
    {

        $this->child = new ArrayObject();
        $this->addChildValue($id);
    }

    public function addChildValue(string $identifier): void
    {
        if(in_array($identifier, $this->child->getArrayCopy(), true))
        {
            return;
        }

        $this->child->append($identifier);
    }

    public function getAllCategoryIdentifier(): array
    {
        return $this->child->getArrayCopy();
    }

    public function getCategoryUrl(): string
    {
        return $this->category_url;
    }

    public function getCategoryName(): string
    {
        return $this->category_name;
    }

    public function getCategoryCoverImage(): ?string
    {
        return $this->category_cover_image;
    }

    public function getCategoryCoverCdn(): bool
    {
        return $this->category_cover_cdn === true;
    }

    public function getCategoryCoverExt(): ?string
    {
        return $this->category_cover_ext;
    }

    public function getLevel(): int
    {
        return $this->level;
    }


    public function setProducts(Generator $MenuPublicProductDTO): void
    {
        $this->products = $MenuPublicProductDTO;
    }

    public function isExistsProduct(): bool
    {
        return $this->products->valid() === true;
    }

    /** @return Generator{int, MenuPublicProductDTO} */
    public function getProducts(): Generator
    {
        return $this->products;
    }
}