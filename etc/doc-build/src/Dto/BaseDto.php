<?php

namespace Oft\Generator\Dto;

abstract class BaseDto
{
    public static function fromArray(array $data): self
    {
        $i = new static();

        foreach ($data as $k => $v) {
            $camelizedK = lcfirst(implode('', array_map('ucfirst', array_map('mb_strtolower', explode('_', $k)))));
            if (\property_exists($i, $camelizedK)) {
                $i->{$camelizedK} = $v;
            }
        }

        return $i;
    }

    public function toArray(): array
    {
        $data = [];

        foreach (\get_object_vars($this) as $k => $v) {
            $data[implode('_', array_map('mb_strtolower', preg_split('/([A-Z]{1}[^A-Z]*)/', $k, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)))] = $v;
        }

        return $data;
    }
}