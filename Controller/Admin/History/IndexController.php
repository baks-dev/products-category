<?php
/*
*  Copyright Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Controller\Admin\History;

use BaksDev\Core\Controller\AbstractController;
use App\Module\Product\Repository\Category\Event\AllHistoryCategory;
use App\Module\Product\Type\Category\Id\CategoryUid;
use BaksDev\Core\Form\Search\Command;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Helper\Paginator;
use BaksDev\Core\Services\Security\RoleSecurity;
use BaksDev\Core\Type\Locale\Locale;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[RoleSecurity(['ROLE_ADMIN', 'ROLE_PRODUCT_CATEGORY_HISTORY'])]
final class IndexController extends AbstractController
{
    #[Route('/admin/product/categorys/history/{id}/{page<\d+>}', name: 'admin.category.history.index',  methods: ['GET', 'POST'])]
    public function history(
        Request $request,
//        AllHistoryCategory $getAllHistoryCategory,
//        string $id,
//        int $page = 0,
    ) : Response
    {
        dd();
        
        /* Поиск */
        $search = new Command();
        $searchForm = $this->createForm(SearchForm::class, $search);
        $searchForm->handleRequest($request);

        /* Получаем список */
        $stmt = $getAllHistoryCategory(new CategoryUid($id), new Locale($request->getLocale()), $search);
        $query = new Paginator($page, $stmt, $request);


        return $this->render(
        [
            'query' => $query,
            'search' => $searchForm->createView(),
        ]);
    }
    
    
}