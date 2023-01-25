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
use BaksDev\Products\Category\Entity as CategoryEntity;
use BaksDev\Products\Category\Type\Event\ProductCategoryEventUid;
use BaksDev\Products\Category\Type\Parent\ProductParentCategoryUid;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Category\CategoryDTO;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Category\CategoryForm;
//use BaksDev\Products\Category\UseCase\CategoryAggregate;
use BaksDev\Core\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[RoleSecurity(['ROLE_ADMIN', 'ROLE_PRODUCT_CATEGORY_NEW'])]
final class NewController extends AbstractController
{
	#[Route('/admin/product/category/new/{cat}/{id}', name: 'admin.newedit.new', defaults: ['cat' => null, "id" => null], methods: ['GET', 'POST'])]

	public function new(
		Request $request,
		//CategoryAggregate $handler,
		#[MapEntity] ?CategoryEntity\ProductCategory $cat,
		?ProductCategoryEventUid $id,
		EntityManagerInterface $entityManager,
	) : Response
	{
		$parent = $cat ? new ProductParentCategoryUid($cat->getId()) : null;
		$category = new CategoryDTO($parent);
		
		$Event = $entityManager->getRepository(CategoryEntity\Event\ProductCategoryEvent::class)->find($id);
		
		/* Копируем данные из события */
		if($Event)
		{
			$Event->getDto($category);
			$category->copy();
		}
		
		/* Форма добавления */
		$form = $this->createForm(CategoryForm::class, $category);
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid())
		{
			$cover = $form->get('cover')->getData();
			$handle = $handler->handle($category, $cover->file);
			
			if($handle)
			{
				$this->addFlash('success', 'admin.new.success', 'products.category');
				return $this->redirectToRoute('ProductCategory:admin.index');
			}
		}
		
		return $this->render(['form' => $form->createView()]);
		
	}
	
}