<?php

namespace App\Services\Voyager\Actions;

use TCG\Voyager\Actions\ViewAction as VoyagerViewAction;

class ViewAction extends VoyagerViewAction
{
    public function getTitle()
    {
        return __('voyager::generic.view');
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right view',
            'target' => '_blank'
        ];
    }

    public function getDefaultRoute()
    {
        return $this->data->url;
        // return route('voyager.'.$this->dataType->slug.'.show', $this->data->{$this->data->getKeyName()});
    }

    public function shouldActionDisplayOnDataType()
    {
        $dataTypes = ['products', 'publications', 'pages'];
        return in_array($this->dataType->slug, $dataTypes);
    }
}
