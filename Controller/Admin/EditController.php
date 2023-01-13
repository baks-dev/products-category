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

namespace App\Module\Products\Category\Controller\Admin;


use App\Module\Products\Category\Entity\Event\Event;
use App\Module\Products\Category\UseCase\Admin\NewEdit\Category\CategoryDTO;
use App\Module\Products\Category\UseCase\Admin\NewEdit\Category\CategoryForm;
use App\Module\Products\Category\UseCase\CategoryAggregate;
use App\System\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_EDIT" in role_names'))]
final class EditController extends AbstractController
{
    
    #[Route('/admin/product/category/edit/{id}', name: 'admin.category.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
      Request $request,
      #[MapEntity] Event $Event,
      CategoryAggregate $handler,
    ) : Response
    {
		
        $category = new CategoryDTO();
        $Event->getDto($category);
		
        /* Форма добавления */
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $cover = $form->get('cover')->getData();
            $handle = $handler->handle($category, $cover->file);
            
            if($handle)
            {
                $this->addFlash('success', 'admin.category.update.success', 'products.category');
                return $this->redirectToRoute('ProductCategory:admin.category.index');
            }
        }
		
        return $this->render(['form' => $form->createView()]);
        
    }

}