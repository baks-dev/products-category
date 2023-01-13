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

namespace App\Module\Products\Category\Entity\Section\Field\Trans;


use App\Module\Products\Category\Entity\Section\Field\Field;
use App\System\Services\EntityEvent\EntityEvent;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Field */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_section_field_trans')]
class Trans extends EntityEvent
{
    public const TABLE = 'product_category_section_field_trans';
    
    /** Связь на поле */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Field::class, inversedBy: "trans")]
    #[ORM\JoinColumn(name: 'field_id', referencedColumnName: 'id')]
    protected Field $field;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    protected string $name;
    
    /** Описание */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description;
    

    public function __construct(Field $field)
    {
        $this->field = $field;
    }
    
    
    
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof TransInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * Метод присваивает свойствам значения из объекта DTO
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {
        if($dto instanceof TransInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    
    public function equals($dto) : bool
    {
        if($dto instanceof TransInterface)
        {
            return  ($this->field->getId() === $dto->getEquals() &&
              $dto->getLocal()?->getValue() === $this->local->getValue());
            
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    
    
    
//    public function updFieldTrans(TransInterface $fieldTrans) : void
//    {
//        if(property_exists($fieldTrans, 'name'))
//        {
//            $this->name = $fieldTrans->name;
//        }
//
//        if(property_exists($fieldTrans, 'description'))
//        {
//            $this->description = $fieldTrans->description;
//        }
//    }
    
    
    
    
    //    /**
//     * @param Field|FieldUid $field
//     */
//    public function __construct(Field|FieldUid $field) {
//
//        $this->field = $field instanceof Field ? $field->getId() : $field;
//    }
//
//    /**
//     * @param Locale $local
//     * @param string $name
//     * @param string|null $desc
//     */
//    public function addFieldTrans(Locale $local, string $name, ?string $desc)
//    {
//        $this->local = $local;
//        $this->name = $name;
//        $this->description = $desc;
//    }

}
