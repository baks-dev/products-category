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

namespace BaksDev\Products\Category\Entity\Section\Field\Trans;


use BaksDev\Products\Category\Entity\Section\Field\Field;
use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Section\Field\ProductCategorySectionField;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Field */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_section_field_trans')]
class ProductCategorySectionFieldTrans extends EntityState
{
	public const TABLE = 'product_category_section_field_trans';
	
	/** Связь на поле */
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: ProductCategorySectionField::class, inversedBy: "translate")]
	#[ORM\JoinColumn(name: 'field', referencedColumnName: 'id')]
	private readonly ProductCategorySectionField $field;
	
	/** Локаль */
	#[ORM\Id]
	#[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
	private readonly Locale $local;
	
	/** Название */
	#[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
	private string $name;
	
	/** Описание */
	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $description;
	
	
	public function __construct(ProductCategorySectionField $field)
	{
		$this->field = $field;
	}
	
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof ProductCategorySectionFieldTransInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof ProductCategorySectionFieldTransInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	//    public function equals($dto) : bool
	//    {
	//        if($dto instanceof TransInterface)
	//        {
	//            return  ($this->field->getId() === $dto->getEquals() &&
	//              $dto->getLocal()?->getValue() === $this->local->getValue());
	//
	//        }
	//
	//        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	//    }
	
	
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
