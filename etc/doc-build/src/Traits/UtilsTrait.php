<?php


namespace Oft\Generator\Traits;


trait UtilsTrait
{
    private function sort(array $arr, string $column = 'code'): array
    {
        $cols = array_column($arr, $column);
        array_multisort($cols, SORT_ASC, $arr);

        return $arr;
    }

    /**
     * @return mixed
     *
     */
    private function array_find(array $arr, callable $callback)
    {
        foreach ($arr as $item) {
            if (call_user_func($callback, $item)) {
                return $item;
            }
        }

        return null;
    }

    private function array_find_index(array $arr, callable $callback): int
    {
        foreach ($arr as $index => $item) {
            if (call_user_func($callback, $item)) {
                return $index;
            }
        }

        return -1;
    }
}
