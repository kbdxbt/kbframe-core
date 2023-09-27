<?php

declare(strict_types=1);

namespace Modules\Core\Http\Requests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * 指示验证是否应在第一个规则失败后停止。
     */
    protected $stopOnFirstFailure = true;

    public function validationData()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function authorize()
    {
        return $this->call(__FUNCTION__, \func_get_args(), true);
    }

    public function rules()
    {
        return $this->call(__FUNCTION__, \func_get_args(), []);
    }

    public function messages()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function attributes()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function validator(ValidationFactory $factory)
    {
        return $this->call(__FUNCTION__, \func_get_args(), $this->createDefaultValidator($factory));
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    protected function failedAuthorization(): void
    {
        $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    protected function withValidator(Validator $validator)
    {
        return $this->call(__FUNCTION__, \func_get_args(), $validator);
    }

    protected function after()
    {
        return $this->call(
            __FUNCTION__,
            \func_get_args(),
            static fn (Validator $validator) => $validator
        );
    }

    protected function call(string $method, array $args = [], $defaultReturn = null)
    {
        $actionMethod = transform($method, function (string $method) {
            if (! \in_array(
                $method,
                [
                    'validationData',
                    'authorize',
                    'rules',
                    'messages',
                    'attributes',
                    'failedValidation',
                    'failedAuthorization',
                    'validator',
                    'withValidator',
                    'after',
                ],
                true
            )) {
                throw new \InvalidArgumentException("Can't call the method[$method].");
            }

            /** @phpstan-ignore-next-line */
            return $this->route()?->getActionMethod().ucfirst($method);
        });

        if (strcasecmp($actionMethod, $method) === 0) {
            return $defaultReturn;
        }

        if (method_exists($this, $actionMethod)) {
            return $this->{$actionMethod}(...$args);
        }

        if (method_exists(parent::class, $method)) {
            return parent::$method(...$args);
        }

        return $defaultReturn;
    }
}
