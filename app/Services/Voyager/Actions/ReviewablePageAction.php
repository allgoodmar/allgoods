<?php

namespace App\Services\Voyager\Actions;

use TCG\Voyager\Actions\ViewAction as VoyagerViewAction;

class ReviewablePageAction extends VoyagerViewAction
{
    public function getTitle()
    {
        return 'Страница';
    }

    public function getIcon()
    {
        return 'voyager-forward';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
            'target' => '_blank'
        ];
    }

    public function getDefaultRoute()
    {
        return $this->data->reviewable->url ?? '';
    }

    public function shouldActionDisplayOnDataType()
    {
        $dataTypes = ['reviews'];
        return in_array($this->dataType->slug, $dataTypes);
    }
}
