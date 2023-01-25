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

namespace BaksDev\Products\Category\Controller\Admin;

use BaksDev\Core\Services\Security\RoleSecurity;
use BaksDev\Products\Category\Entity\Category;
use BaksDev\Products\Category\Repository\AllCategory\AllCategoryInterface;
use BaksDev\Products\Category\Type\Parent\ParentCategoryUid;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Helper\Paginator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RoleSecurity(['ROLE_ADMIN', 'ROLE_PRODUCT_CATEGORY'])]
final class IndexController extends AbstractController
{
	
	#[Route('/admin/product/categorys/{cat}', name: 'admin.category.index', defaults: ['cat' => null], methods: ['GET', 'POST'])]
	public function index(
		Request $request,
		AllCategoryInterface $allCategory,
		#[MapEntity] ?Category $cat,
	) : Response
	{
		
		/* Поиск */
		$search = new SearchDTO();
		$searchForm = $this->createForm(SearchForm::class, $search);
		$searchForm->handleRequest($request);
		
		/* Получаем список */
		$parent = $cat ? new ParentCategoryUid($cat->getId()) : null;
		$stmt = $allCategory->get($search, $parent);
		$query = new Paginator(0, $stmt, $request);
		
		return $this->render(
			[
				'query' => $query,
				'search' => $searchForm->createView(),
				'parent' => null,// ($cat ? $getParentCategory->get($cat) : null), /* Получаем корневую директорию */
				'parent_id' => $parent,
			]
		);
	}
	
	//    #[Route('/624b3490a3c9d/style', name: 'admin.category.index.css', methods: ['GET'], format: "css")]
	//    public function css() : Response
	//    {
	//        return $this->assets();
	//    }
	//
	//    #[Route('/624b3490a3c9d/app', name: 'admin.category.index.js', methods: ['GET'], format: "js")]
	//    public function js() : Response
	//    {
	//        return $this->assets();
	//    }
	
	
}