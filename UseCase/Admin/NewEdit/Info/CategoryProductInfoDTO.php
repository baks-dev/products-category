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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Info;

use BaksDev\Products\Category\Entity\Info\CategoryProductInfoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryProductInfoDTO implements CategoryProductInfoInterface
{
    /** Семантическая ссылка на раздел (строка с тире и нижним подчеркиванием) */
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-z0-9\_\-]+$/i'
    )]
    private ?string $url = null;

    /** Статус активности раздела */
    private bool $active = false;


    /** Минимальное количество в заказе ниже которого запрещается оформить заказ */
    #[Assert\NotBlank]
    private int $minimal = 1;

    /** Количество по умолчанию (предзаполняет форму) */
    #[Assert\NotBlank]
    private int $input = 1;

    /** Порог наличия продукции (default 10) @example «более 10» | «менее 10» */
    #[Assert\NotBlank]
    private int $threshold = 10;

    public function getMinimal(): int
    {
        return $this->minimal;
    }

    public function setMinimal(int $minimal): self
    {
        $this->minimal = $minimal;

        return $this;
    }

    public function getInput(): int
    {
        return $this->input;
    }

    public function setInput(int $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): self
    {
        $this->threshold = $threshold;

        return $this;
    }


    /** Семантическая ссылка на раздел */

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /** Статус активности раздела */

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function updateUrlUniq(): void
    {
        $this->url = uniqid($this->url.'_', false);
    }

}
