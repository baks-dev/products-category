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

namespace BaksDev\Products\Category\Entity\Section;

use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Products\Category\Type\Section\Id\SectionUid;
use BaksDev\Core\Services\EntityEvent\EntityEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Секуция торгового предложения */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_section')]
class ProductCategorySection extends EntityEvent
{
    public const TABLE = 'product_category_section';
    
    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: SectionUid::TYPE)]
    protected SectionUid $id;
    
    /** Связь на событие */
    #[ORM\ManyToOne(targetEntity: Event::class, cascade: ["remove", "persist"], inversedBy: "sections")]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: true)]
    protected ?Event $event;
    
    /** Перевод */
    #[ORM\OneToMany(mappedBy: 'section', targetEntity: Trans\Trans::class, cascade: ['all'])]
    protected Collection $trans;
    
    /** Поля секции */
    #[ORM\OneToMany(mappedBy: 'section', targetEntity: Field\Field::class, cascade: ['all'])]
    protected Collection $fields;
    
    /** Сортировка */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false, options: ['default' => 100])]
    protected int $sort = 100;

    public function __construct(Event $event) {
        
        $this->id = new SectionUid();
        $this->event = $event;
        
        $this->trans = new ArrayCollection();
        //$this->getTrans();
        
        
        $this->fields = new ArrayCollection();
        //$this->getFields();
        
    }
    
//    public function __clone()
//    {
//        $this->id = new SectionUid();
//    }
 
	public function __toString() : string
	{
		return $this->id;
	}
	
	/**
     * @return SectionUid
     */
    public function getId() : SectionUid
    {
        return $this->id;
    }
    
    
    
    /**
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof SectionInterface)
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
        if($dto instanceof SectionInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    public function removeElement() : void
    {
        $this->event = null;
    }
    
    
    protected function equals($dto) : bool
    {
        if($dto instanceof SectionInterface)
        {
            return $this->id === $dto->getEquals();
        }
    
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
}