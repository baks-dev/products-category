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

use App\Module\Products\Category\Entity;
use App\Module\Products\Category\UseCase\Admin\Delete\Category\CategoryDTO;
use App\Module\Products\Category\UseCase\Admin\Delete\DeleteForm;
use App\Module\Products\Category\UseCase\CategoryAggregate;
use App\System\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_DELETE" in role_names'))]
final class DeleteController extends AbstractController
{
    
    #[Route('/admin/product/category/delete/{id}', name: 'admin.category.delete', methods: ['POST', 'GET'])]
    public function delete(
      Request $request,
      CategoryAggregate $handler,
      Entity\Event\Event $Event,
    ) : Response
    {
        
        $category = new CategoryDTO();
        $Event->getDto($category);
        
        $form = $this->createForm(DeleteForm::class, $category, [
          'action' => $this->generateUrl('ProductCategory:admin.category.delete', ['id' => $category->getEvent()]),
        ]);
        $form->handleRequest($request);
        
        
        if($form->isSubmitted() && $form->isValid())
        {
            if($form->has('delete'))
            {
                $handle = $handler->handle($category);
                
                if($handle)
                {
                    $this->addFlash('success', 'admin.category.delete.success', 'products.category');
                    return $this->redirectToRoute('ProductCategory:admin.category.index');
                }
            }
            
            $this->addFlash('danger', 'admin.category.update.danger', 'products.category');
            return $this->redirectToRoute('ProductCategory:admin.category.index');
            
            //return $this->redirectToReferer();
        }

        return $this->render
        (
          [
            'form' => $form->createView(),
            'name' => $Event->getNameByLocale($this->getLocale()) /*  название согласно локали  */
          ]
        );
    }
    
}