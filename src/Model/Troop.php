<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 26/10/20
 * Time: 9:00
 */

namespace App\Model;


class Troop
{
    /**
     * @var int
     */
    private $level;

    /**
     * @var string
     */
    private $name;

    const LEVEL_MIN = 20;
    const LEVEL_MAX = 100;

    public function setRandomLevel(): void
    {
        $this->level = random_int(self::LEVEL_MIN, self::LEVEL_MAX);
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getName():string
    {
        return $this->name;
    }
}
