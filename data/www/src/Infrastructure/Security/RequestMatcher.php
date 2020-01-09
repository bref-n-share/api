<?php

namespace App\Infrastructure\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestMatcher implements RequestMatcherInterface
{
    private array $patterns;

    public function __construct(array $patterns)
    {
        $patternsOptionResolver = new OptionsResolver();
        $patternsOptionResolver->setRequired([
            'pattern'
        ]);
        $patternsOptionResolver->setDefaults([
            'excluded_patterns' => [],
        ]);

        $this->patterns = $patternsOptionResolver->resolve($patterns);
    }

    /**
     * @inheritDoc
     */
    public function matches(Request $request)
    {
        $uri = $request->getUri();

        // if match main pattern
        if (preg_match($this->formatPattern($this->patterns['pattern']), $uri)) {
            // then test if is an excluded pattern
            foreach ($this->patterns['excluded_patterns'] as $excludedPattern) {
                if (preg_match($this->formatPattern($excludedPattern['pattern']), $uri)
                    && strtoupper($excludedPattern['method']) === $request->getMethod()) {
                    return false;
                }
            }
        }

        return true;
    }

    private function formatPattern($pattern): string
    {
        return rtrim($pattern, '^') . '^';
    }
}
