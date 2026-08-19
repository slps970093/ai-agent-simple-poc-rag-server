<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\SearchInsurancePolicyTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Insurance Policy Server')]
#[Version('1.0.0')]
#[Instructions('Use the search_insurance_policy tool to retrieve active insurance policy terms. Only answer from returned policy text and cite the policy code, version, and source pages. If no relevant terms are returned, state that the available policy text is insufficient.')]
class InsurancePolicyServer extends Server
{
    protected array $tools = [
        SearchInsurancePolicyTool::class,
    ];
}
