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

namespace BaksDev\Products\Category\Repository\CategoryPropertyById;

use BaksDev\Core\Type\Field\InputField;
use BaksDev\Products\Category\Type\Section\Field\Id\CategoryProductSectionFieldUid;
use BaksDev\Products\Category\Type\Section\Id\CategoryProductSectionUid;
use Symfony\Component\Validator\Constraints as Assert;

final class PropertyCategoryDTO
{
    /** Идентификатор Секции */
    public CategoryProductSectionUid $sectionUid;

    /** Перевод секции */
    public string $sectionTrans;

    /** Идентификатор поля */
    public CategoryProductSectionFieldUid $fieldUid;

    /** Перевод поля */
    public string $fieldTrans;

    /** Опсиание поля */
    public ?string $fieldDesc;

    /** Тип поля (input|select|integer|textarea|.....)  */
    public InputField $fieldType;

    /** Обязательное к заполнению поле */
    public bool $fieldRequired;


    /**
     * $dbal->addSelect('section.id AS section');
     * $dbal->addSelect('section_trans.name AS section_trans');
     * $dbal->addSelect('field.const AS field');
     * $dbal->addSelect('field_trans.name AS field_trans');
     * $dbal->addSelect('field.type AS field_type');
     * $dbal->addSelect('field.required AS required');
     * $dbal->addSelect('field_trans.description AS description');
     */
    public function __construct(
        string $section,
        string $section_trans,
        string $field,
        string $field_trans,
        string $field_type,
        bool $required,
        ?string $description,
    )
    {

        $this->sectionUid = new CategoryProductSectionUid($section);   /* ID Секции  */
        $this->sectionTrans = $section_trans; /* Перевод секции */

        $this->fieldUid = new CategoryProductSectionFieldUid($field); /* ID поля   */
        $this->fieldTrans = $field_trans; /* Перевод поля */
        $this->fieldType = new InputField($field_type); /* Тип поля (input|select|integer|textarea|.....) */
        $this->fieldRequired = $required; /* Обязательное к заполнению поле */
        $this->fieldDesc = $description; /* Описание */
    }

}
