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

declare(strict_types=1);

namespace BaksDev\Products\Category\Entity\Offers\Variation\Modification\Trans;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Offers\Variation\Modification\CategoryProductModification;
use BaksDev\Products\Category\Type\Offers\Modification\CategoryProductModificationUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/* Перевод ProductCategoryOffersVariationTrans */


#[ORM\Entity]
#[ORM\Table(name: 'product_category_modification_trans')]
#[ORM\Index(columns: ['name'])]
class CategoryProductModificationTrans extends EntityState
{
    public const TABLE = 'product_category_modification_trans';

    /** Связь на событие */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: CategoryProductModification::class, inversedBy: "translate")]
    #[ORM\JoinColumn(name: 'modification', referencedColumnName: "id")]
    private readonly CategoryProductModification $modification;

    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2)]
    private readonly Locale $local;

    /** Название модификации */
    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name;

    /** Название постфикса */
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $postfix = null;

    public function __construct(CategoryProductModification $modification)
    {
        $this->modification = $modification;
    }

    public function __toString(): string
    {
        return (string) $this->modification;
    }

    /**
     * Modification
     */
    public function getModification(): CategoryProductModificationUid
    {
        return $this->modification->getId();
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryProductModificationTransInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {

        if($dto instanceof CategoryProductModificationTransInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function name(Locale $locale): ?string
    {
        if($this->local->getLocalValue() === $locale->getLocalValue())
        {
            return $this->name;
        }

        return null;
    }
}
