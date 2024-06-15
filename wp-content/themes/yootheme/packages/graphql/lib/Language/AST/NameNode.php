<?php declare(strict_types=1);

namespace YOOtheme\GraphQL\Language\AST;

class NameNode extends Node implements TypeNode
{
    public string $kind = NodeKind::NAME;

    public string $value;
}
