<?php

namespace App\Infrastructure\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestMatcher implements RequestMatcherInterface
{
    private array $patterns;

    private const PARAMETERS_REGEX = '(((\?)([A-Za-z0-9-_=&]*)?)?)';

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
        // Remove '$' from the current pattern (if existing)
        if ($isStrictPattern = substr($pattern, -1) === '$') {
            $pattern = substr($pattern, 0, -1);
        }

        // Added regex to match '?param1=XX&param2=XX'
        $pattern .= self::PARAMETERS_REGEX;

        // Add '$' to the current pattern
        if ($isStrictPattern) {
            $pattern .= '$';
        }

        return rtrim($pattern, '^') . '^';
    }
}
