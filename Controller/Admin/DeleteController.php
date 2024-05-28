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

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\UseCase\Admin\Delete\DeleteCategoryProductDTO;
use BaksDev\Products\Category\UseCase\Admin\Delete\DeleteProductCategoryForm;
use BaksDev\Products\Category\UseCase\Admin\Delete\DeleteProductCategoryHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_PRODUCT_CATEGORY_DELETE')]
final class DeleteController extends AbstractController
{
    #[Route('/admin/product/category/delete/{id}', name: 'admin.delete', methods: ['POST', 'GET'])]
    public function delete(
        Request $request,
        DeleteProductCategoryHandler $handler,
        Entity\Event\CategoryProductEvent $Event,
    ): Response
    {
        $category = new DeleteCategoryProductDTO();
        $Event->getDto($category);
        $form = $this->createForm(DeleteProductCategoryForm::class, $category, [
            'action' => $this->generateUrl('products-category:admin.delete', ['id' => $category->getEvent()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('delete'))
        {
            $this->refreshTokenForm($form);

            $ProductCategory = $handler->handle($category);
            if($ProductCategory instanceof Entity\CategoryProduct)
            {
                $this->addFlash('admin.form.header.delete', 'admin.success.delete', 'admin.products.category');

                return $this->redirectToRoute('products-category:admin.index');
            }
            $this->addFlash(
                'admin.form.header.delete',
                'admin.danger.delete',
                'admin.products.category',
                $ProductCategory
            );

            return $this->redirectToRoute('products-category:admin.index', status: 400);
        }

        return $this->render(
            [
                'form' => $form->createView(),
                'name' => $Event->getNameByLocale($this->getLocale()), // название согласно локали
            ]
        );
    }
}
