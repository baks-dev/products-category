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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Category;


use BaksDev\Products\Category\Repository\ParentCategoryChoiceForm\ParentCategoryChoiceFormInterface;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoryForm extends AbstractType
{
    
    private ParentCategoryChoiceFormInterface $categoryParent;

    public function __construct(ParentCategoryChoiceFormInterface $categoryParent)
    {
        $this->categoryParent = $categoryParent;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        /* TextType */
        $builder->add('sort', IntegerType::class);
    
        $builder->add('cover', NewEdit\Cover\CoverForm::class);
        
        $builder->add('info', NewEdit\Info\InfoForm::class);
        
        
//        $builder
//          ->add(
//            'cover', FileType::class,
//            [
//              'label' => false,
//              'required' => false,
//              'attr' => ['accept' => ".png, .jpg, .jpeg"],
//            ]
//          );
        

        
        //dd($options['data']->getParentCategory());
        


        $builder->add(
          'parent',
          ChoiceType::class,
          [
              'label' => false,
              'required' => false,
              'choices' => $this->categoryParent->get(),
              'choice_value' => function (?ParentCategoryUid $type)
              {
                  return $type?->getValue();
              },
              'choice_label' => function (ParentCategoryUid $type)
              {
                  return $type->getName();
              },
          ]);
        
        
        /* Category Trans */
        $builder->add('trans', CollectionType::class, [
          'entry_type' => Trans\CategoryTransForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);
        
        /* Seo Collection */
        $builder->add('seo', CollectionType::class, [
          'entry_type' => NewEdit\Seo\SeoCollectionForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);
        
        /* Landing Collection */
        $builder->add('landings', CollectionType::class, [
          'entry_type' => NewEdit\Landing\LandingCollectionForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);
        
        /* Section Collection */
        $builder->add('sections', CollectionType::class, [
          'entry_type' => NewEdit\Section\SectionCollectionForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);
        
        /* Offers Collection */
        $builder->add('offers', CollectionType::class, [
          'entry_type' => NewEdit\Offers\OffersCollectionForm::class,
          'entry_options' => ['label' => false],
          'label' => false,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
        ]);
        
    }
    
    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults
        (
          [
            'data_class' => CategoryDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
          ]);
    }
    
}
