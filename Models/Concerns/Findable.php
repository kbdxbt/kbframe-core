<?php

declare(strict_types=1);

namespace Modules\Core\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static \Illuminate\Database\Eloquent\Builder findWhere(array $where, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder findByField($field, $value = null, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder findWhereIn($field, array $values, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder findWhereNotIn($field, array $values, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder findWhereBetween($field, array $values, $columns = ['*'])
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Findable
{
    public function scopeFindWhere(Builder $query, array $where, $columns = ['*'])
    {
        $this->applyConditions($query, $where);

        return $query->get($columns);
    }

    protected function applyConditions(Builder $query, array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                [$field, $condition, $val] = $value;
                //smooth input
                $condition = preg_replace('/\s\s+/', ' ', trim($condition));

                //split to get operator, syntax: "DATE >", "DATE =", "DAY <"
                $operator = explode(' ', $condition);
                if (count($operator) > 1) {
                    $condition = $operator[0];
                    $operator = $operator[1];
                } else {
                    $operator = null;
                }

                switch (strtoupper($condition)) {
                    case 'IN':
                        if (! is_array($val)) {
                            throw new \InvalidArgumentException("Input {$val} mus be an array");
                        }
                        $query->whereIn($field, $val);

                        break;

                    case 'NOTIN':
                        if (! is_array($val)) {
                            throw new \InvalidArgumentException("Input {$val} mus be an array");
                        }
                        $query->whereNotIn($field, $val);

                        break;

                    case 'DATE':
                        if (! $operator) {
                            $operator = '=';
                        }
                        $query->whereDate($field, $operator, $val);

                        break;

                    case 'DAY':
                        if (! $operator) {
                            $operator = '=';
                        }
                        $query->whereDay($field, $operator, $val);

                        break;

                    case 'MONTH':
                        if (! $operator) {
                            $operator = '=';
                        }
                        $query->whereMonth($field, $operator, $val);

                        break;

                    case 'YEAR':
                        if (! $operator) {
                            $operator = '=';
                        }
                        $query->whereYear($field, $operator, $val);

                        break;

                    case 'EXISTS':
                        if (! ($val instanceof \Closure)) {
                            throw new \InvalidArgumentException("Input {$val} must be closure function");
                        }
                        $query->whereExists($val);

                        break;

                    case 'HAS':
                        if (! ($val instanceof \Closure)) {
                            throw new \InvalidArgumentException("Input {$val} must be closure function");
                        }
                        $query->whereHas($field, $val);

                        break;

                    case 'HASMORPH':
                        if (! ($val instanceof \Closure)) {
                            throw new \InvalidArgumentException("Input {$val} must be closure function");
                        }
                        $query->whereHasMorph($field, $val);

                        break;

                    case 'DOESNTHAVE':
                        if (! ($val instanceof \Closure)) {
                            throw new \InvalidArgumentException("Input {$val} must be closure function");
                        }
                        $query->whereDoesntHave($field, $val);

                        break;

                    case 'DOESNTHAVEMORPH':
                        if (! ($val instanceof \Closure)) {
                            throw new \InvalidArgumentException("Input {$val} must be closure function");
                        }
                        $query->whereDoesntHaveMorph($field, $val);

                        break;

                    case 'BETWEEN':
                        if (! is_array($val)) {
                            throw new \InvalidArgumentException("Input {$val} mus be an array");
                        }
                        $query->whereBetween($field, $val);

                        break;

                    case 'BETWEENCOLUMNS':
                        if (! is_array($val)) {
                            throw new \InvalidArgumentException("Input {$val} mus be an array");
                        }
                        $query->whereBetweenColumns($field, $val);

                        break;

                    case 'NOTBETWEEN':
                        if (! is_array($val)) {
                            throw new \InvalidArgumentException("Input {$val} mus be an array");
                        }
                        $query->whereNotBetween($field, $val);

                        break;

                    case 'NOTBETWEENCOLUMNS':
                        if (! is_array($val)) {
                            throw new \InvalidArgumentException("Input {$val} mus be an array");
                        }
                        $query->whereNotBetweenColumns($field, $val);

                        break;

                    case 'RAW':
                        $query->whereRaw($val);

                        break;

                    default:
                        $query->where($field, $condition, $val);
                }
            } else {
                $query->where($field, '=', $value);
            }
        }
    }

    public function scopeFindByField(Builder $query, $field, $value = null, $columns = ['*'])
    {
        return $query->where($field, '=', $value)->first($columns);
    }

    public function scopeFindWhereIn(Builder $query, $field, array $values, $columns = ['*'])
    {
        return $query->whereIn($field, $values)->get($columns);
    }

    public function scopeFindNotWhereIn(Builder $query, $field, array $values, $columns = ['*'])
    {
        return $query->whereNotIn($field, $values)->get($columns);
    }

    public function scopeFindWhereBetween(Builder $query, $field, array $values, $columns = ['*'])
    {
        return $query->whereBetween($field, $values)->get($columns);
    }
}
