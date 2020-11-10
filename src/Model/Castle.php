<?php

/**
 * Created by VisualStudioCode.
 * User: Thomas
 * Date: 30/10/2020
 * Time: 19:00
 */

namespace App\Model;

/**
 * This class allows you to create a castle with a name property and a score initialized to 0.
 */
class Castle
{
    public const POSSIBLE_NAMES = ["Kaamelott", "Barad-dûr", "Winterfell", "Defend the Castle"];

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $score;

    public function resetScore(): void
    {
        $this->score = 0;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setName(string $name): void
    {
        if (in_array($name, self::POSSIBLE_NAMES)) {
            $this->name = $name;
        } else {
            $this->name = self::POSSIBLE_NAMES[3];
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
