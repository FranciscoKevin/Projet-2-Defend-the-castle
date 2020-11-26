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
    private string $name;
    private int $score;

    public function resetScore(): void
    {
        $this->score = 0;
    }

    public function setScore(int $newCastleScore)
    {
        $this->score = $newCastleScore;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
