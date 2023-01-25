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

namespace BaksDev\Products\Category\Controller\Admin;


use BaksDev\Core\Services\Security\RoleSecurity;
use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Category\CategoryDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Category\CategoryForm;
use BaksDev\Products\Category\UseCase\CategoryAggregate;
use BaksDev\Core\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[RoleSecurity(['ROLE_ADMIN', 'ROLE_PRODUCT_CATEGORY_EDIT'])]
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