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

namespace BaksDev\Products\Category\Repository\AllRecyclebinCategory;

use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Helper\Switcher\Switcher;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Core\Type\Modify\ModifyActionEnum;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use BaksDev\Products\Category\Entity as EntityCategory;

final class AllRecyclebinCategoryQuery implements AllRecyclebinCategoryInterface
{
	private Connection $connection;
	private Locale $local;
	
	public function __construct(Connection $connection, TranslatorInterface $translator)
	{
		$this->connection = $connection;
		$this->local = new Locale($translator->getLocale());
		
	}
	
	public function get(SearchDTO $search) : QueryBuilder
	{
		$stmt = $this->connection->createQueryBuilder();
		
		$stmt->select('category_modify.mod_date');
		$stmt->addSelect('category_modify.user_ip');
		$stmt->addSelect('category_modify.user_agent');
		
		$stmt->from(EntityCategory\Modify\Modify::TABLE, 'category_modify');
		
		
		$stmt->addSelect('category_event.category as id');
		$stmt->addSelect('category_event.id as event');
		//$stmt->addSelect('category_event.cover');
		
		$stmt->leftJoin(
			'category_modify',
			EntityCategory\Event\Event::TABLE,
			'category_event',
			'category_event.id = category_modify.event'
		);
		
		
		$stmt->addSelect('category_trans.name');
		$stmt->addSelect('category_trans.description');
		
		$stmt->leftJoin(
			'category_event',
			EntityCategory\Trans\Trans::TABLE,
			'category_trans',
			'category_trans.event = category_event.id AND category_trans.local = :local'
		);
		
		$stmt->setParameter('local', $this->local, Locale::TYPE);
		
		
		// $stmt->leftJoin('category_modify', User::TABLE, 'users', 'users.id = category_modify.user_id');
		
		
		//        $stmt->addSelect('user_event.id as user_id');
		//        $stmt->addSelect(' user_event.user_email');
		//        $stmt->leftJoin('users', User\Event::TABLE, 'user_event', 'user_event.id = users.event');
		
		
		$stmt->where('category_modify.action = :action');
		$stmt->setParameter('action', ModifyActionEnum::DELETE, ModifyAction::TYPE);
		
		/* Поиск */
		if($search->query)
		{
			$stmt->andWhere(
				'
                        LOWER(category_trans.name) LIKE :query OR
                        LOWER(category_trans.description) LIKE :query OR                         LOWER(category_modify.mod_user_agent) LIKE :query OR
                        LOWER(user_event.user_email) LIKE :query OR

                        LOWER(category_trans.name) LIKE :switcher OR
                        LOWER(category_trans.description) LIKE :switcher OR                         LOWER(user_event.user_email) LIKE :switcher OR
                        LOWER(category_modify.mod_user_agent) LIKE :switcher

                    '
			);
			
			$stmt->setParameter('query', '%'.mb_strtolower((new Switcher())($search->query, 0)).'%');
			$stmt->setParameter('switcher', '%'.mb_strtolower((new Switcher())($search->query, 1)).'%');
		}
		
		$stmt->orderBy('category_modify.mod_date', 'DESC');
		
		return $stmt;
	}
	
}