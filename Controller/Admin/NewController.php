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

use App\Module\Products\Category\Entity as CategoryEntity;
use App\Module\Products\Category\Type\Event\CategoryEvent;
use App\Module\Products\Category\Type\Parent\ParentCategoryUid;
use App\Module\Products\Category\UseCase\Admin\NewEdit\Category\CategoryDTO;
use App\Module\Products\Category\UseCase\Admin\NewEdit\Category\CategoryForm;
use App\Module\Products\Category\UseCase\CategoryAggregate;
use App\System\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_NEW" in role_names'))]
final class NewController extends AbstractController
{
	#[Route('/admin/product/category/new/{cat}/{id}', name: 'admin.category.newedit.new', defaults: ['cat' => null, "id" => null], methods: ['GET', 'POST'])]

	public function new(
		Request $request,
		CategoryAggregate $handler,
		#[MapEntity] ?CategoryEntity\Category $cat,
		?CategoryEvent $id,
		EntityManagerInterface $entityManager,
	) : Response
	{
		$parent = $cat ? new ParentCategoryUid($cat->getId()) : null;
		$category = new CategoryDTO($parent);
		
		$Event = $entityManager->getRepository(CategoryEntity\Event\Event::class)->find($id);
		
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
				$this->addFlash('success', 'admin.category.new.success', 'products.category');
				return $this->redirectToRoute('ProductCategory:admin.category.index');
			}
		}
		
		return $this->render(['form' => $form->createView()]);
		
	}
	
}