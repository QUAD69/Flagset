<?php

namespace Quad69;

class Flagset implements \Stringable
{
    public const LENGTH = 255;

    private string $content;

    public function __construct(string $content = '')
    {
        $this->content = str_pad($content, self::LENGTH, "\0");
    }

    public function set(int $num, bool $value): static
    {
        if ($num >= 0 and $num < self::LENGTH)
        {
            $byte = intdiv($num, 8);
            $bit = pow(2, $num % 8);

            $char = $this->content[$byte];
            $char = ord($char);
            $value ? $char |= $bit : $char &= ~$bit;
            $char = chr($char);
            $this->content[$byte] = $char;
        }

        return $this;
    }

    public function has(int $num): bool
    {
        $byte = intdiv($num, 8);
        $bit = pow(2, $num % 8);

        return (ord($this->content[$byte]) & $bit) > 0;
    }

    public function append(string $flagset): static
    {
        $this->content |= $flagset; return $this;
    }

    public function subtract(string $flagset): static
    {
        $this->content &= ~$flagset; return $this;
    }

    public function __toString()
    {
        return $this->content;
    }

    public static function getNulled(): string
    {
        return str_repeat("\0", self::LENGTH);
    }

    public static function getFilled(): string
    {
        return str_repeat("\xff", self::LENGTH);
    }
}