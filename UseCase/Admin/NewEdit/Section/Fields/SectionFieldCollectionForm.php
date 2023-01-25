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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Fields;

use BaksDev\Core\Type\Field\InputField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SectionFieldCollectionForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        /* TextType */
        $builder->add
        (
          'sort',
          IntegerType::class,
          [
            'label' => false,
            'attr' => ['min' => 0, 'max' => 999]
          ]
        );
        
        /* CollectionType */
        $builder->add('trans', CollectionType::class, [
          'entry_type' => Trans\SectionFieldTransForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);


        $builder
          ->add('type', ChoiceType::class, [
            'choices' => InputField::cases(),  // array_flip(Main::LANG),
            'choice_value' => function (?InputField $type)
            {
                return $type?->getValue();
            },
            'choice_label' => function (InputField $type)
            {
                return $type->getValue();
            },

            'label' => false,
            'expanded' => false,
            'multiple' => false,
            'required' => true,
          ]);
    
        
        /* Обязательное к заполнению */
        $builder->add('required', CheckboxType::class, [
          'required' => false,
        ]);
    
        /* Публичное свойтсво */
        $builder->add('public', CheckboxType::class, [
          'required' => false,
        ]);
    
        $builder->add
        (
          'DeleteField',
          ButtonType::class,
          [
            'label_html' => true,
            'attr' =>
              ['class' => 'btn btn-sm btn-icon btn-light-danger del-item-field'],
          ]);
        
  
    }
    
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults
        (
          [
            'data_class' => SectionFieldCollectionDTO::class,
          ]);
    }
    
}
