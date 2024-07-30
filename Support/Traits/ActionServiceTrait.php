<?php

declare(strict_types=1);

namespace Modules\Core\Support\Traits;

trait ActionServiceTrait
{
    protected $repository;

    public function getList($params): array
    {
        $query = $this->repository->query();

        $this->repository->searchable($query, $params);

        $result = $query->pageList($params)->toArray();

        return $this->formatListData($this->formatList($result));
    }

    public function cursorPageList($params, $isTotal = true): array
    {
        $query = $this->repository->query();

        $this->repository->searchable($query, $params);

        $result = $query->cursorPageList($params)->toArray();

        if (empty($result['next_cursor']) && $isTotal) {
            $result['total'] = $query->count();
        }

        return $this->formatListData($this->formatList($result));
    }

    public function saveData($params): void
    {
        $this->repository->updateOrInsert([
            'id' => $params['id'] ?? 0
        ], $params);
    }

    public function getDetail($keyValue, $keyName = ''): array
    {
        $keyName = $keyName ? : $this->repository->getModel()->getKeyName();
        return $this->repository->query()->where([$keyName => $keyValue])->firstToArray();
    }

    public function deleteData($params): void
    {
        $this->repository->delete($params['ids'], $params['is_force'] ?? false);
    }

    public function export($params): array
    {
        $params['page_size'] = config('app.export_page_size');
        return $this->formatListData($this->cursorPageList($params), 'label');
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

    protected function formatList($data)
    {
        return $data;
    }

    public function formatListData($result, $mapKey = 'key'): array
    {
        $result['data'] = collect($result['data'])->map(function ($item) use ($mapKey) {
            if ($searchFields = $this->repository->searchFields()) {
                return collect($searchFields)->mapWithKeys(function ($label, $key) use ($item, $mapKey) {
                    return [($mapKey == 'key' ? $key : $label) => $item[$key] ?? ''];
                });
            }

            return $item;
        })->all();

        return $result;
    }
}
