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

namespace App\Module\Products\Category\UseCase\Admin\NewEdit\Offers;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OffersCollectionForm extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder->add
        (
          'sort',
          IntegerType::class,
          [
            'label' => false,
            'attr' => ['min' => 0, 'max' => 999]
          ]
        );
        
        /* Множественный выбор справочника */
        $builder->add('multiple', CheckboxType::class, ['required' => false,]);
    
    
        /* Торговое предложение - Справочник */
        $builder->add('isReference', CheckboxType::class, ['mapped' => false, 'required' => false,]);
        
        /** Если ранее выбран справочник - выделяем чекбокс  */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $product = $event->getData();
            $form = $event->getForm();
        
            if ($product && $product->getReference() !== null) {
                $form->add('isReference', CheckboxType::class, ['mapped' => false, 'required' => false, 'data' => true,]);
            }
        });
        
        
        /* Справочники */
        $builder->add
        ('reference',
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
           'translation_domain' => 'reference'
         ]
        );
    
        $builder->add('image', CheckboxType::class, ['required' => false]);
    
        $builder->add('price', CheckboxType::class, ['required' => false]);
        
        $builder->add('quantitative', CheckboxType::class, ['required' => false]);
        
        $builder->add('article', CheckboxType::class, ['required' => false]);
    
        /* Offers Trans */
        $builder->add('trans', CollectionType::class, [
          'entry_type' => Trans\OffersTransForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);
    
        $builder->add
        (
          'DeleteOffers',
          ButtonType::class,
          [
            'label_html' => true,
            'attr' =>
              ['class' => 'btn btn-sm  btn-light-danger del-item-offers'],
          ]);
        

        
    }
    
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults
        (
          [
            'data_class' => OffersCollectionDTO::class,
          ]);
    }
    
}
