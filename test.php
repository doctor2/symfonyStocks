<?php

class Test
{
    public function formSquare(int $sideLength): string
    {
        $square = '';

        for ($i = 1; $i <= $sideLength; $i++) {
            for ($j = 1; $j <= $sideLength; $j++) {
                if ($i === 1 || $i === $sideLength || $i === $j || $j === 1 || $j === $sideLength || $j + $i === $sideLength + 1) {
                    $square .= '#';
                } else {
                    $square .= '.';
                }
            }

            $square .= PHP_EOL;
        }

        return $square;
    }

    public function findExcelColumn(int $columnNumber): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabetLength = strlen($alphabet);

        $division = (int) floor($columnNumber / $alphabetLength);

        if ($division === 0) {
            return $alphabet[$columnNumber - 1];
        }

        $alphabetPosition = $columnNumber - $division * $alphabetLength;

        return $this->findExcelColumn($division) . $alphabet[$alphabetPosition - 1];
    }

    public function convertStdObjectToArray(stdClass $stdObject): array
    {
        return (array) $stdObject;
    }
}

$test = new Test();

var_dump($test->findExcelColumn(1022));
var_dump($test->formSquare(7));

$std = new stdClass;
$std->aaaa = 'aaaa';
$std->bbbb = 'bbbb';
var_dump($test->convertStdObjectToArray($std));
