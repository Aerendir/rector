<?php

declare(strict_types=1);

namespace Rector\Nette\Rector\Identical;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\Variable;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\Nette\ValueObject\ContentExprAndNeedleExpr;

/**
 * @see https://github.com/nette/utils/blob/master/src/Utils/Strings.php
 * @see \Rector\Nette\Tests\Rector\Identical\EndsWithFunctionToNetteUtilsStringsRector\EndsWithFunctionToNetteUtilsStringsRectorTest
 */
final class EndsWithFunctionToNetteUtilsStringsRector extends AbstractWithFunctionToNetteUtilsStringsRector
{
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Use Nette\Utils\Strings::endWith() over bare string-functions', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function end($needle)
    {
        $content = 'Hi, my name is Tom';

        $yes = substr($content, -strlen($needle)) === $needle;
    }
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    public function end($needle)
    {
        $content = 'Hi, my name is Tom';

        $yes = \Nette\Utils\Strings::endsWith($content, $needle);
    }
}
PHP
            ),
        ]);
    }

    protected function getMethodName(): string
    {
        return 'endsWith';
    }

    protected function matchContentAndNeedleOfSubstrOfVariableLength(
        Node $node,
        Variable $variable
    ): ?ContentExprAndNeedleExpr {
        if (! $this->isFuncCallName($node, 'substr')) {
            return null;
        }

        /** @var FuncCall $node */
        if (! $node->args[1]->value instanceof UnaryMinus) {
            return null;
        }

        /** @var UnaryMinus $unaryMinus */
        $unaryMinus = $node->args[1]->value;

        if (! $this->isFuncCallName($unaryMinus->expr, 'strlen')) {
            return null;
        }

        /** @var FuncCall $strlenFuncCall */
        $strlenFuncCall = $unaryMinus->expr;

        if ($this->areNodesEqual($strlenFuncCall->args[0]->value, $variable)) {
            return new ContentExprAndNeedleExpr($node->args[0]->value, $strlenFuncCall->args[0]->value);
        }

        return null;
    }
}
