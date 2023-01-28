<?php declare(strict_types=1);

namespace n3tw0rk\Streetlamp\Attributes\DataBindings\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
class JsonIgnore
{

}