<?php

use App\Mcp\Servers\InsurancePolicyServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/insurance-policies', InsurancePolicyServer::class)
    ->middleware('auth.bot');
