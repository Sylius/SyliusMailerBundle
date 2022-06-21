<?php

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTagTypeFixer;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ECSConfig $ecsConfig): void
{
    $ecsConfig->import('vendor/sylius-labs/coding-standard/ecs.php');

    $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, [
        'location' => 'after_open',
        'header' =>
            'This file is part of the Sylius package.

(c) Paweł Jędrzejewski

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.',
    ]);

    $ecsConfig->skip([
        PhpdocTagTypeFixer::class,
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        VisibilityRequiredFixer::class => ['*Spec.php'],
        '**/var/*',
        'src/Bundle/test/app/AppKernel.php',
    ]);
};
