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

namespace App\Module\Products\Category\Repository\CategoryPropertyById;


use App\Module\Products\Category\Type\Section\Field\Id\FieldUid;
use App\Module\Products\Category\Type\Section\Id\SectionUid;
use App\System\Type\Field\InputField;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryPropertyDTO
{
    
    /**
     * ID Секции
     * @var SectionUid
     */
    public SectionUid $sectionUid;
    
    /**
     * Перевод секции
     * @var string
     */
    public string $sectionTrans;
    
    /**
     * ID поля
     * @var FieldUid
     */
    public FieldUid $fieldUid;
    
    /**
     * Перевод поля
     * @var string
     */
    public string $fieldTrans;
    
    /**
     * Перевод поля
     * @var ?string
     */
    public ?string $fieldDesc;
    
    /**
     * Тип поля (input|select|integer|textarea|.....)
     * @var string
     */
    public string $fieldType;
    
    /**
     * Обязательное к заполнению поле
     * @var bool
     */
    public bool $fieldRequired;
    
    /**
     * @param SectionUid $sectionUid
     * @param string $sectionTrans
     * @param FieldUid $fieldUid
     * @param string $fieldTrans
     * @param InputField $fieldType
     * @param bool $fieldRequired
     * @param ?string $fieldDesc
     */
    public function __construct(
      SectionUid $sectionUid,
      string $sectionTrans,
      FieldUid $fieldUid,
      string $fieldTrans,
      InputField $fieldType,
      bool $fieldRequired,
      ?string $fieldDesc,
    
    )
    {
        $this->sectionUid = $sectionUid;   /* ID Секции  */
        $this->sectionTrans = $sectionTrans; /* Перевод секции */
        
        $this->fieldUid = $fieldUid; /* ID поля   */
        $this->fieldTrans = $fieldTrans; /* Перевод поля */
        $this->fieldType = $fieldType->getType()->value; /* Тип поля (input|select|integer|textarea|.....) */
        $this->fieldRequired = $fieldRequired; /* Обязательное к заполнению поле */
        $this->fieldDesc = $fieldDesc; /* Описание */
    }
    
}

