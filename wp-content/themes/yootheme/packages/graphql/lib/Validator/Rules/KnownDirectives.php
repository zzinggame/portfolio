<?php declare(strict_types=1);

namespace YOOtheme\GraphQL\Validator\Rules;

use YOOtheme\GraphQL\Error\Error;
use YOOtheme\GraphQL\Error\InvariantViolation;
use YOOtheme\GraphQL\Language\AST\DirectiveDefinitionNode;
use YOOtheme\GraphQL\Language\AST\DirectiveNode;
use YOOtheme\GraphQL\Language\AST\EnumTypeDefinitionNode;
use YOOtheme\GraphQL\Language\AST\EnumTypeExtensionNode;
use YOOtheme\GraphQL\Language\AST\EnumValueDefinitionNode;
use YOOtheme\GraphQL\Language\AST\FieldDefinitionNode;
use YOOtheme\GraphQL\Language\AST\FieldNode;
use YOOtheme\GraphQL\Language\AST\FragmentDefinitionNode;
use YOOtheme\GraphQL\Language\AST\FragmentSpreadNode;
use YOOtheme\GraphQL\Language\AST\InlineFragmentNode;
use YOOtheme\GraphQL\Language\AST\InputObjectTypeDefinitionNode;
use YOOtheme\GraphQL\Language\AST\InputObjectTypeExtensionNode;
use YOOtheme\GraphQL\Language\AST\InputValueDefinitionNode;
use YOOtheme\GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use YOOtheme\GraphQL\Language\AST\InterfaceTypeExtensionNode;
use YOOtheme\GraphQL\Language\AST\Node;
use YOOtheme\GraphQL\Language\AST\NodeKind;
use YOOtheme\GraphQL\Language\AST\NodeList;
use YOOtheme\GraphQL\Language\AST\ObjectTypeDefinitionNode;
use YOOtheme\GraphQL\Language\AST\ObjectTypeExtensionNode;
use YOOtheme\GraphQL\Language\AST\OperationDefinitionNode;
use YOOtheme\GraphQL\Language\AST\ScalarTypeDefinitionNode;
use YOOtheme\GraphQL\Language\AST\ScalarTypeExtensionNode;
use YOOtheme\GraphQL\Language\AST\SchemaDefinitionNode;
use YOOtheme\GraphQL\Language\AST\SchemaExtensionNode;
use YOOtheme\GraphQL\Language\AST\UnionTypeDefinitionNode;
use YOOtheme\GraphQL\Language\AST\UnionTypeExtensionNode;
use YOOtheme\GraphQL\Language\AST\VariableDefinitionNode;
use YOOtheme\GraphQL\Language\DirectiveLocation;
use YOOtheme\GraphQL\Language\Visitor;
use YOOtheme\GraphQL\Type\Definition\Directive;
use YOOtheme\GraphQL\Validator\QueryValidationContext;
use YOOtheme\GraphQL\Validator\SDLValidationContext;
use YOOtheme\GraphQL\Validator\ValidationContext;

/**
 * @phpstan-import-type VisitorArray from Visitor
 */
class KnownDirectives extends ValidationRule
{
    /** @throws InvariantViolation */
    public function getVisitor(QueryValidationContext $context): array
    {
        return $this->getASTVisitor($context);
    }

    /** @throws InvariantViolation */
    public function getSDLVisitor(SDLValidationContext $context): array
    {
        return $this->getASTVisitor($context);
    }

    /**
     * @phpstan-return VisitorArray
     *
     * @throws InvariantViolation
     */
    public function getASTVisitor(ValidationContext $context): array
    {
        $locationsMap = [];
        $schema = $context->getSchema();
        $definedDirectives = $schema === null
            ? Directive::getInternalDirectives()
            : $schema->getDirectives();

        foreach ($definedDirectives as $directive) {
            $locationsMap[$directive->name] = $directive->locations;
        }

        $astDefinition = $context->getDocument()->definitions;

        foreach ($astDefinition as $def) {
            if ($def instanceof DirectiveDefinitionNode) {
                $locationNames = [];
                foreach ($def->locations as $location) {
                    $locationNames[] = $location->value;
                }

                $locationsMap[$def->name->value] = $locationNames;
            }
        }

        return [
            NodeKind::DIRECTIVE => function (
                DirectiveNode $node,
                $key,
                $parent,
                $path,
                $ancestors
            ) use (
                $context,
                $locationsMap
            ): void {
                $name = $node->name->value;
                $locations = $locationsMap[$name] ?? null;

                if ($locations === null) {
                    $context->reportError(new Error(
                        static::unknownDirectiveMessage($name),
                        [$node]
                    ));

                    return;
                }

                $candidateLocation = $this->getDirectiveLocationForASTPath($ancestors);

                if ($candidateLocation === '' || \in_array($candidateLocation, $locations, true)) {
                    return;
                }

                $context->reportError(
                    new Error(
                        static::misplacedDirectiveMessage($name, $candidateLocation),
                        [$node]
                    )
                );
            },
        ];
    }

    public static function unknownDirectiveMessage(string $directiveName): string
    {
        return "Unknown directive \"@{$directiveName}\".";
    }

    /**
     * @param array<Node|NodeList> $ancestors
     *
     * @throws \Exception
     */
    protected function getDirectiveLocationForASTPath(array $ancestors): string
    {
        $appliedTo = $ancestors[\count($ancestors) - 1];

        switch (true) {
            case $appliedTo instanceof OperationDefinitionNode:
                switch ($appliedTo->operation) {
                    case 'query':
                        return DirectiveLocation::QUERY;
                    case 'mutation':
                        return DirectiveLocation::MUTATION;
                    case 'subscription':
                        return DirectiveLocation::SUBSCRIPTION;
                }
                // no break, since all possible cases were handled
            case $appliedTo instanceof FieldNode:
                return DirectiveLocation::FIELD;
            case $appliedTo instanceof FragmentSpreadNode:
                return DirectiveLocation::FRAGMENT_SPREAD;
            case $appliedTo instanceof InlineFragmentNode:
                return DirectiveLocation::INLINE_FRAGMENT;
            case $appliedTo instanceof FragmentDefinitionNode:
                return DirectiveLocation::FRAGMENT_DEFINITION;
            case $appliedTo instanceof VariableDefinitionNode:
                return DirectiveLocation::VARIABLE_DEFINITION;
            case $appliedTo instanceof SchemaDefinitionNode:
            case $appliedTo instanceof SchemaExtensionNode:
                return DirectiveLocation::SCHEMA;
            case $appliedTo instanceof ScalarTypeDefinitionNode:
            case $appliedTo instanceof ScalarTypeExtensionNode:
                return DirectiveLocation::SCALAR;
            case $appliedTo instanceof ObjectTypeDefinitionNode:
            case $appliedTo instanceof ObjectTypeExtensionNode:
                return DirectiveLocation::OBJECT;
            case $appliedTo instanceof FieldDefinitionNode:
                return DirectiveLocation::FIELD_DEFINITION;
            case $appliedTo instanceof InterfaceTypeDefinitionNode:
            case $appliedTo instanceof InterfaceTypeExtensionNode:
                return DirectiveLocation::IFACE;
            case $appliedTo instanceof UnionTypeDefinitionNode:
            case $appliedTo instanceof UnionTypeExtensionNode:
                return DirectiveLocation::UNION;
            case $appliedTo instanceof EnumTypeDefinitionNode:
            case $appliedTo instanceof EnumTypeExtensionNode:
                return DirectiveLocation::ENUM;
            case $appliedTo instanceof EnumValueDefinitionNode:
                return DirectiveLocation::ENUM_VALUE;
            case $appliedTo instanceof InputObjectTypeDefinitionNode:
            case $appliedTo instanceof InputObjectTypeExtensionNode:
                return DirectiveLocation::INPUT_OBJECT;
            case $appliedTo instanceof InputValueDefinitionNode:
                $parentNode = $ancestors[\count($ancestors) - 3];

                return $parentNode instanceof InputObjectTypeDefinitionNode
                    ? DirectiveLocation::INPUT_FIELD_DEFINITION
                    : DirectiveLocation::ARGUMENT_DEFINITION;
            default:
                $unknownLocation = \get_class($appliedTo);
                throw new \Exception("Unknown directive location: {$unknownLocation}.");
        }
    }

    public static function misplacedDirectiveMessage(string $directiveName, string $location): string
    {
        return "Directive \"{$directiveName}\" may not be used on \"{$location}\".";
    }
}
