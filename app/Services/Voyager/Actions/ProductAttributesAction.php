<?php

namespace App\Services\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ProductAttributesAction extends AbstractAction
{
	public function getTitle()
    {
        return 'Атрибуты';
    }

    public function getIcon()
    {
        return 'voyager-categories';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right m-5',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.products.attributes.edit', ['product' => $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        $dataTypes = ['products'];
        return in_array($this->dataType->slug, $dataTypes);
    }
}
