<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);
    $ecsConfig->skip([
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::SPACES,
        SetList::ARRAY,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::COMMENTS,
        SetList::CLEAN_CODE,
    ]);
    $ecsConfig->rulesWithConfiguration([
        ConcatSpaceFixer::class => ['spacing' => 'none'],
        CastSpacesFixer::class => ['space' => 'none'],
        FunctionDeclarationFixer::class => ['closure_fn_spacing' => 'none'],
    ]);
    $ecsConfig->skip([
        NotOperatorWithSuccessorSpaceFixer::class,
        ArrayOpenerAndCloserNewlineFixer::class,
    ]);
};
