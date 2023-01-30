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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Offers\Variation;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductCategoryOffersVariationForm extends AbstractType
{
	
	public function buildForm(FormBuilderInterface $builder, array $options) : void
	{
		
		/** Торговое предложение - Справочник */
		$builder->add('isReference', CheckboxType::class, ['mapped' => false, 'required' => false,]);
		
		
		/** Если ранее выбран справочник - выделяем чекбокс  */
		$builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
			$product = $event->getData();
			$form = $event->getForm();
			
			if($product && $product->getReference() !== null)
			{
				$form->add('isReference', CheckboxType::class, ['mapped' => false, 'required' => false, 'data' => true,]
				);
			}
		});
		
		
		/** Справочники */
		$builder->add
		(
			'reference',
			ChoiceType::class,
			[
				'required' => false,
				'choices' => [
					'reference.color' => 'color', /* Цвет */
					'reference.clothing.size' => 'size_clothing', /* Размер одежды */
					'reference.clothing.child' => 'child_size_clothing', /* Детский Размер одежды */
					'reference.pants' => 'pants', /* Размер брюк (джинс) */
					
					//'reference.shoe.size' => 'size_shoe', /* Размер обуви */
				],
				'translation_domain' => 'reference',
			]
		);
		
		$builder->add('image', CheckboxType::class, ['required' => false]);
		
		$builder->add('price', CheckboxType::class, ['required' => false]);
		
		$builder->add('quantitative', CheckboxType::class, ['required' => false]);
		
		$builder->add('article', CheckboxType::class, ['required' => false]);
		
		/* Offers Trans */
		$builder->add('translate', CollectionType::class, [
			'entry_type' => Trans\ProductCategoryOffersVariationTransForm::class,
			'entry_options' => ['label' => false],
			'label' => false,
			'by_reference' => false,
			'allow_delete' => true,
			'allow_add' => true,
			'prototype_name' => '__offer_translate__',
		]);
		
		/** Флаг, то что торговое предложение имеет множественные варианты
		 * !!! Должен распологаться ниже свойства translate
		 */
		$builder->add('variation', CheckboxType::class, ['required' => false, 'label' => false]);
		
		
	}
	
	public function configureOptions(OptionsResolver $resolver) : void
	{
		$resolver->setDefaults([
			'data_class' => ProductCategoryOffersVariationDTO::class,
		]);
	}
}