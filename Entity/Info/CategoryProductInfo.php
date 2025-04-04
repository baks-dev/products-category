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

namespace BaksDev\Products\Category\Entity\Info;

use BaksDev\Core\Entity\EntityState;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/* Неизменяемые данные Категории */


#[ORM\Entity]
#[ORM\Table(name: 'product_category_info')]
#[ORM\Index(columns: ['active'])]
#[ORM\Index(columns: ['url'])]
class CategoryProductInfo extends EntityState
{
    /** Связь на событие */
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: CategoryProductEvent::class, inversedBy: 'info', fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private ?CategoryProductEvent $event;

    /** Семантическая ссылка на раздел */
    #[ORM\Column(type: Types::STRING)]
    private string $url;

    /** Статус активности раздела */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    /** Количество товаров в разделе */
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $counter = 0;

    /**
     * Минимальное количество в заказе ниже которого запрещается оформить заказ
     */
    #[Assert\Range(min: 1)]
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    private int $minimal = 1;

    /**
     * Количество по умолчанию (предзаполняет форму)
     */
    #[Assert\Range(min: 1)]
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    private int $input = 1;

    /**
     * Порог наличия продукции (default 10)
     * @example «более 10» | «менее 10»
     */
    #[Assert\Range(min: 1)]
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 10])]
    private int $threshold = 10;

    /**
     * Отобразить карточку как модель
     */
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $model = false;


    public function __construct(CategoryProductEvent $event)
    {
        $this->event = $event;
    }

    public function __toString(): string
    {
        return (string) $this->event;
    }

    public function getCategory(): CategoryProductUid
    {
        return $this->event->getCategory();
    }

    public function getEvent(): ?CategoryProductEvent
    {
        return $this->event;
    }

    public function getUrl(): string
    {
        return $this->url;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof CategoryProductInfoInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof CategoryProductInfoInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function add(): void
    {
        ++$this->counter;
    }

    public function sub(): void
    {
        --$this->counter;
    }

}
