<?php

namespace Modules\Core\Support\Traits;

/**
 * 检查操作Trait类
 */
trait CheckActionTrait
{
    /**
     * 是否为列表数据请求
     */
    public function actionOfGetData(): bool
    {
        return request()->_action == 'getData';
    }

    /**
     * 是否为导出数据请求
     */
    public function actionOfExport(): bool
    {
        return request()->_action == 'export';
    }

    /**
     * 是否为快速编辑数据请求
     */
    public function actionOfQuickEdit(): bool
    {
        return request()->_action == 'quickEdit';
    }

    /**
     * 是否为快速编辑数据请求
     */
    public function actionOfQuickEditItem(): bool
    {
        return request()->_action == 'quickEditItem';
    }
}
