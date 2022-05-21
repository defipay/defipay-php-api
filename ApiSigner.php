<?php

namespace Defipay\api;

interface ApiSigner
{
    public function sign($message): string;

    public function getPublicKey(): string;
}