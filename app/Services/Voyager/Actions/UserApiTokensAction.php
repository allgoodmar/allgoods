<?php

namespace App\Services\Voyager\Actions;

use TCG\Voyager\Actions\AbstractAction;

class UserApiTokensAction extends AbstractAction
{
	public function getTitle()
    {
        return 'API Токен';
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
            'class' => 'btn btn-sm btn-primary pull-right mx-1',
        ];
    }

    public function getDefaultRoute()
    {
        return route('voyager.users.api_tokens', ['user' => $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        $dataTypes = ['users'];
        return in_array($this->dataType->slug, $dataTypes);
    }
}
