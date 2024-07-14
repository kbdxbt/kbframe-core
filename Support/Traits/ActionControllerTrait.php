<?php

declare(strict_types=1);

namespace Modules\Core\Support\Traits;

use Modules\Core\Exceptions\BadRequestException;

trait ActionControllerTrait
{
    use JsonResponseable;

    protected $request;

    protected $service;

    public function list()
    {
        return $this->success($this->service->getList($this->request->validateInput()));
    }

    public function save()
    {
        $this->service->saveData($this->request->validateInput());

        return $this->ok();
    }

    public function detail()
    {
        return $this->success($this->service->getDetail($this->request->id));
    }

    public function delete()
    {
        $this->service->deleteData($this->request->validateInput());

        return $this->ok();
    }
}
