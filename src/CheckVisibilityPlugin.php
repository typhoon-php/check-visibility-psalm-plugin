<?php

declare(strict_types=1);

namespace Typhoon\CheckVisibilityPsalmPlugin;

use PhpParser\Node\Stmt\Function_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\AfterFunctionLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

/**
 * @api
 */
final class CheckVisibilityPlugin implements PluginEntryPointInterface, AfterFunctionLikeAnalysisInterface, AfterClassLikeVisitInterface
{
    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $class = $event->getStorage();

        if ($event->getStmt()->name !== null && !$class->internal && !$class->public_api) {
            $class->docblock_issues[] = new UnspecifiedVisibility(
                'Class ' . $class->name,
                $class->location ?? new CodeLocation($event->getStatementsSource(), $event->getStmt()),
            );
        }
    }

    public static function afterStatementAnalysis(AfterFunctionLikeAnalysisEvent $event): ?bool
    {
        $function = $event->getFunctionlikeStorage();

        if ($event->getStmt() instanceof Function_ && $function->cased_name !== null && !$function->internal && !$function->public_api) {
            IssueBuffer::accepts(
                new UnspecifiedVisibility(
                    'Function ' . $function->cased_name,
                    $function->location ?? new CodeLocation($event->getStatementsSource(), $event->getStmt()),
                ),
                $event->getStatementsSource()->getSuppressedIssues(),
            );
        }

        return null;
    }

    public function __invoke(RegistrationInterface $registration, ?\SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
        require_once __DIR__ . '/UnspecifiedVisibility.php';
    }
}
