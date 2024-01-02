<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use ReflectionClass;

class EnumValue implements Rule
{
    /**
     * @var string
     */
    private $enumClass;

    /**
     * Create a new rule instance.
     *
     * @param string $enumClass
     */
    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function passes($attribute, $value): bool
    {
        $enumClass = new ReflectionClass($this->enumClass);

        return $enumClass->hasConstant($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        $enumClass = new ReflectionClass($this->enumClass);

        return "The :attribute must be a valid value of the enum " .
            $enumClass->getShortName();
    }
}
