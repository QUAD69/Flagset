<?php
declare(strict_types=1);

namespace Quad69;

/**
 * Класс для работы с большим набором флагов (true/false) и хранения
 * их в компактном (бинарном) виде. Также есть методы для объединения
 * или исключения одной группы флагов из другой.
 *
 * Тип колонки для MySQL - BINARY(255)
 *
 * @author QUAD69 <https://vk.com/quad69>
 */
class Flagset implements \Stringable
{
    /**
     * Фиксированная длина строки для флагов.
     */
    public const LENGTH = 255;

    private string $content;

    public function __construct(string $content = '')
    {
        $this->content = str_pad($content, self::LENGTH, "\0");
    }

    /**
     * Задает значение указанному флагу
     *
     * @param int $num Номер флага (от 0 до 255) который необходимо изменить
     * @param bool $value Значение true/false для флага
     * @return static
     */
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

    /**
     * Проверяет значение указанного флага
     *
     * @param int $num Номер флага (от 0 до 255) который необходимо проверить
     * @return bool Значение флага (true/false)
     */
    public function has(int $num): bool
    {
        $byte = intdiv($num, 8);
        $bit = pow(2, $num % 8);

        return (ord($this->content[$byte]) & $bit) > 0;
    }
    
    /**
     * Добавить флаги из другого объекта Flagset или строки (побитовое ИЛИ)
     *
     * @param string $flagset Flagset или строка для объединения
     * @return static
     */
    public function append(string $flagset): static
    {
        $this->content |= $flagset; return $this;
    }
    
    /**
     * Вычесть флаги из другого объекта Flagset или строки (побитовое И НЕ)
     *
     * @param string $flagset Flagset или строка для вычетания
     * @return static
     */
    public function subtract(string $flagset): static
    {
        $this->content &= ~$flagset; return $this;
    }

    /**
     * Получить FlagSet где все флаги заданы в значение false
     *
     * @return static
     */
    public static function getNulled(): static
    {
        return new static(str_repeat("\0", self::LENGTH));
    }
    
    /**
     * Получить FlagSet где все флаги заданы в значение true
     *
     * @return static
     */
    public static function getFilled(): static
    {
        return new static(str_repeat("\xff", self::LENGTH));
    }
    
    public function __toString()
    {
        return $this->content;
    }
}
