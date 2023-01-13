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

namespace App\Module\Products\Category\Entity;


use App\Module\Products\Category\Type\Settings\CategorySettings;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/* Настройки сущности Category */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_settings')]
class Settings
{
    public const TABLE = 'product_category_settings';

    /** ID */
    #[ORM\Id]
    #[ORM\Column(type: CategorySettings::TYPE)]
    private CategorySettings $id;

    /** Очищать корзину старше n дней */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false)]
    private int $truncate = 365;
    
    
    /** Очищать события старше n дней */
    #[ORM\Column(type: Types::SMALLINT, length: 3, nullable: false)]
    private int $history = 365;

    public function __construct() { $this->id = new CategorySettings();  }
    
    /**
     * @param int $settingsTruncate
     * @param int $settingsHistory
     */
    public function setSettings(int $settingsTruncate, int $settingsHistory) : void
    {
        $this->truncate = $settingsTruncate;
        $this->history = $settingsHistory;
    }
    
}
