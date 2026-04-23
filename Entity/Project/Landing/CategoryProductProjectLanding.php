<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Entity\Project\Landing;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Device\Device;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Project\CategoryProductProject;
use BaksDev\Products\Category\Type\Project\Landing\CategoryProductProjectLandingUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* CategoryProductProjectLanding */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_project_landing')]
class CategoryProductProjectLanding extends EntityState
{

    #[ORM\Id]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: CategoryProductProjectLandingUid::TYPE)]
    private ?CategoryProductProjectLandingUid $id;


    /** Связь на CategoryProductProject */
    #[ORM\ManyToOne(targetEntity: CategoryProductProject::class, inversedBy: "landing")]
    #[ORM\JoinColumn(name: 'project', referencedColumnName: 'id')]
    private readonly CategoryProductProject $project;


    /** Локаль */
    #[ORM\Column(type: Locale::TYPE, length: 2)]
    private readonly Locale $local;


    /** Верхний блок */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $header;


    /** Нижний блок */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bottom;


    /** Девайс (pc, mobile, tablet) */
    #[ORM\Column(type: Device::TYPE, nullable: false, options: ['default' => 'pc'])]
    private Device $device;


    public function __construct(CategoryProductProject $project)
    {
        $this->project = $project;
        $this->id = new CategoryProductProjectLandingUid();
    }


    public function __toString(): string
    {
        return (string) $this->project;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryProductProjectLandingInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {

        if($dto instanceof CategoryProductProjectLandingInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}