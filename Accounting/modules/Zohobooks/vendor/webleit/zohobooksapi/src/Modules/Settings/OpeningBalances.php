<?php

namespace Webleit\ZohoBooksApi\Modules\Settings;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Support\Collection;
use Webleit\ZohoBooksApi\Models\Settings\OpeningBalance;
use Webleit\ZohoBooksApi\Modules\Module;

/**
 * Class OpeningBalances
 * @package Webleit\ZohoBooksApi\Modules
 */
class OpeningBalances extends Module
{
    /**
     * @return string
     */
    public function getUrlPath()
    {
        return 'settings/openingbalances';
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return OpeningBalance::class;
    }

    /**
     * @return string
     */
    public function getResourceKey()
    {
        return 'opening_balance';
    }

    /**
     * @return Collection
     */
    public function getList($params = [])
    {
        return new Collection([$this->get()]);
    }

    /**
     * @param string $id
     * @param array $params
     * @return \Webleit\ZohoBooksApi\Models\Model
     */
    public function get ($id, array $params = [])
    {
        return parent::get($id, $params); // TODO: Change the autogenerated stub
    }


}