<?php

declare(strict_types=1);

namespace Tests;

final class Relation
{
    public string $mainResourceType;
    public string $identityParamNameOfMain;
    public string $identityParamNameOfRelatedInMain;

    public string $relatedResourceType;
    public string $identityParamNameOfRelated;

    public function __construct(
        string $mainResourceType,
        string $identityParamNameOfRelatedInMain,
        string $relatedResourceType,
        string $identityParamNameOfMain = 'code',
        string $identityParamNameOfRelated = 'code'
    ) {
        $this->mainResourceType = $mainResourceType;
        $this->identityParamNameOfRelatedInMain = $identityParamNameOfRelatedInMain;
        $this->relatedResourceType = $relatedResourceType;
        $this->identityParamNameOfMain = $identityParamNameOfMain;
        $this->identityParamNameOfRelated = $identityParamNameOfRelated;
    }
}