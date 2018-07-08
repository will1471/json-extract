<?php

namespace {

    require_once __DIR__ . '/../src/Collector.php';
    require_once __DIR__ . '/../src/Lexer.php';
    require_once __DIR__ . '/../src/Parser.php';
    require_once __DIR__ . '/../src/Token.php';
    require_once __DIR__ . '/../src/UnexpectedToken.php';
}

namespace Will1471\JsonExtract {

    function help()
    {
        echo "usage: cat somefile | php json-extract.php o=1 | jq .\n";
        die(1);
    }

    $type = null;
    $index = null;

    if ($argc === 2) {
        $filter = $argv[1];
        $bits = explode('=', $filter);
        if (count($bits) !== 2) {
            help();
        }
        [$type, $index] = $bits;
    }

    if ($type === null || !in_array($type, ['a', 'o'], true)) {
        help();
    }

    if ($index === null || !ctype_digit($index)) {
        help();
    }

    $index = (int)$index;

    while ($line = fgets(STDIN)) {
        $l = new Lexer($line);
        $p = new Parser($l);
        $c = new Collector();
        $p->parse($c);

        $data = null;
        if ($type === 'a') {
            $data = $c->arrayAtIndex($index);
        }
        if ($type === 'o') {
            $data = $c->objectAtIndex($index);
        }
        if ($data !== null) {
            echo $data . "\n";
        }
    }
}
