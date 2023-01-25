<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Products\Category\Entity\Cover;

use App\Module\Files\Res\Upload\UploadEntityInterface;
use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Core\Services\EntityEvent\EntityEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Обложка раздела */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_cover')]
class ProductCategoryCover extends EntityEvent implements UploadEntityInterface
{
    public const TABLE = 'product_category_cover';

    /** Связь на событие */
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'cover', targetEntity: Event::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id')]
    protected Event $event;
    
    /** Название директории */
    #[ORM\Column(type: CategoryEvent::TYPE, nullable: false)]
    protected CategoryEvent $dir;
    
    /** Название файла */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 100, nullable: false)]
    protected string $name;
    
    /** Расширение файла */
    #[ORM\Column(name: 'ext', type: Types::STRING, length: 64, nullable: false)]
    protected string $ext;
    
    /** Размер файла */
    #[ORM\Column(name: 'size', type: Types::INTEGER, nullable: false)]
    private int $size = 0;
    
    /** Файл загружен на CDN */
    #[ORM\Column(name: 'cdn', type: Types::BOOLEAN, nullable: false)]
    protected bool $cdn = false;
    
    /**
     * @param Event $event
     */
    public function __construct(Event $event) { $this->event = $event; }
    
    /**
     * @return Event
     */
    public function getId() : Event
    {
        return $this->event;
    }
    
//    /**
//     * @return Event
//     */
//    public function getEvent() : CategoryEvent
//    {
//        return $this->event->getId();
//    }
    
    
    
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof ProductCategoryCoverInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {

        /* Если размер файла нулевой - не заполняем сущность */
        if(
          (empty($dto->file) && empty($dto->getName())) ||
          (!empty($dto->file) && empty($dto->getName()))
        )
        {
            return false;
        }

        if($dto instanceof ProductCategoryCoverInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    public function updFile(string $name, string $ext, int $size) : void
    {
        $this->cdn = false;
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
        $this->dir = $this->event->getId();
        $this->cdn = false;
    }

    public function updCdn(string $ext): void
    {
        $this->ext = $ext;
        $this->cdn = true;
    }
    
    
    public function getUploadDir() : object
    {
        return $this->event->getId();
    }
    
}