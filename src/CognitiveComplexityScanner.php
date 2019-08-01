<?php

/**
 * Copyright (c) 2019 Potherca
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Potherca\Complexity;

use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\CognitiveComplexityAnalyzer as Analyzer;
use PHP_CodeSniffer\Tokenizers\PHP as Tokenizer;

class CognitiveComplexityScanner
{
    /** @var Analyzer */
    private $analyzer;
    /** @var Tokenizer */
    private $tokenizer;

    final public function __construct(Tokenizer $tokenizer, Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
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
            $complexity[] = 1 + $this->analyzer->computeForFunctionFromTokensAndPosition(
                $tokens,
                $position
            );
        });

        return $complexity;
    }
}
