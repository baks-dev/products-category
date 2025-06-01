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

use BaksDev\Reference\Currency\Type\Currency;
use BaksDev\Reference\Money\Type\Money;
use Symfony\Component\Validator\Constraints as Assert;

/** @see MenuPublicCategory */
final readonly class MenuPublicProductDTO
{
    public function __construct(
        private string $url, // " => "triangle_pl01"
        private string $name, // " => "Triangle PL01"
        private int $min_price, // " => 650000
        private ?string $product_currency, // " => 650000
        private int $total, // " => 36
        private string|null $profile_discount = null,

        private string|null $product_image = null,
        private string|null $product_image_ext = null,
        private bool|null $product_image_cdn = null,

    ) {}

    public function getProductUrl(): string
    {
        return $this->url;
    }

    public function getProductName(): string
    {
        return $this->name;
    }

    public function getProductPrice(): Money|false
    {
        if(empty($this->min_price))
        {
            return false;
        }

        $price = new Money($this->min_price, true);

        // применяем скидку пользователя из профиля
        if(false === empty($this->profile_discount))
        {
            $price->applyString($this->profile_discount);
        }

        return $price;
    }

    public function getProductTotal(): int
    {
        return $this->total;
    }

    public function getProductImage(): ?string
    {
        return $this->product_image;
    }

    public function getProductImageExt(): ?string
    {
        return $this->product_image_ext;
    }

    public function getProductImageCdn(): bool
    {
        return $this->product_image_cdn === true;
    }

    public function getProductCurrency(): Currency
    {
        return new Currency($this->product_currency);
    }
}