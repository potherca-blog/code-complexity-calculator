<?php

namespace Potherca\Complexity;

use PHP_CodeSniffer\Tokenizers\PHP as Tokenizer;

class CyclomaticComplexityScanner
{
    /** @var Tokenizer */
    private $tokenizer;

    final public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    final public function calculate()
    {
        $tokens = $this->tokenizer->getTokens();

        // @TODO: Figure out class name so it can be added here...

        $functions = [];
        array_walk($tokens, function ($token, $position) use (&$functions) {
            if ($token['code'] === T_FUNCTION) {
                // @TODO: Figure out function name so it can be added here...
                $functions[] = $position;
            }
        });

        $complexity = [];
        array_walk($functions, function ($position) use (&$complexity, $tokens) {
            $complexity[] = $this->analyze($tokens, $position);
        });

        return $complexity;
    }

    public function analyze(array $tokens, $position)
    {
        $complexity = 1;

        // Ignore abstract methods.
        if (isset($tokens[$position]['scope_opener']) === false) {
            return;
        }

        // Detect start and end of this function definition.
        $start = $tokens[$position]['scope_opener'];
        $stop = $tokens[$position]['scope_closer'];

        // Predicate nodes for PHP.
        $find = [
            T_CASE    => true,
            T_DEFAULT => true,
            T_CATCH   => true,
            T_IF      => true,
            T_FOR     => true,
            T_FOREACH => true,
            T_WHILE   => true,
            T_DO      => true,
            T_ELSEIF  => true,
        ];

        // Iterate from start to end and count predicate nodes.
        for ($i = ($start + 1); $i < $stop; $i++) {
            if (isset($find[$tokens[$i]['code']]) === true) {
                $complexity++;
            }
        }

        return $complexity;
    }
}
