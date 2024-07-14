<?php

declare(strict_types=1);

namespace Modules\Core\Support\Traits;

use Modules\Core\Exceptions\BadRequestException;

trait ActionServiceTrait
{
    protected $repository;

    public function getList($params): array
    {
        $query = $this->repository->query();

        $this->repository->searchable($query, $params);

        return $query->pageList($params)->toArray();
    }

    public function cursorPageList($params, $isTotal = true): array
    {
        $query = $this->repository->query();

        $this->repository->searchable($query, $params);

        $result = $query->cursorPageList($params)->toArray();

        if (empty($result['next_cursor']) && $isTotal) {
            $result['total'] = $query->count();
        }

        return $result;
    }

    public function saveData($params): void
    {
        $this->repository->updateOrInsert([
            'id' => $params['id'] ?? 0
        ], $params);
    }

    public function getDetail($id): array
    {
        $data = $this->repository->query()->find($id)?->toArray();
        if (!$data) {
            throw new BadRequestException('没有找到符合条件的记录');
        }

        return $data;
    }

    public function deleteData($params): void
    {
        $this->repository->delete($params['ids'], $params['is_force'] ?? false);
    }

    public function export($params): array
    {
        $params['page_size'] = config('app.export_page_size');
        return $this->formatExportData($this->cursorPageList($params));
    }

    public function formatExportData($result): array
    {
        $result['data'] = collect($result['data'])->map(function ($item) {
            return collect($this->repository->searchFields())->mapWithKeys(function ($label, $key) use ($item) {
                return [$label => $item[$key] ?? ''];
            });
        })->all();

        return $result;
    }

    public function import($list): array
    {
        $failData = [];
        foreach ($list as $v) {
            try {
                $this->saveData($v);
            } catch (\Exception $e) {
                $v['错误提示'] = $e->getMessage();
                $failData[] = $v;
            }
        }

        return $failData;
    }

    public function importTemplate(): string
    {
        return fastexcel([
            collect($this->repository->searchFields())->flip()->map(function () {
                return '';
            })->all()
        ])->export('php://output');
    }
}
