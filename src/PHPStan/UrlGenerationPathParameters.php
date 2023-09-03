<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use Illuminate\Routing\UrlGenerator;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\Support\Reflection;
use PhpParser\Node;

use function array_diff;
use function array_flip;
use function array_key_exists;
use function array_shift;
use function array_values;
use function count;

/**
 * Models the parameter array passed to a function generating a URL to a named
 * route.
 *
 * E.g. the second argument in
 * ```
 * route('articles.comments.delete', ['article' => 1, 'comment' => 2]);
 * ```
 *
 * This is not always as simple. Consider the example of the above route which
 * is defined using:
 * ```
 * Route::get('/articles/{article}/comments/{comment}', function() {});
 * ```
 *
 * Instead of the first example, we could also generate the URL with
 * ```
 * route('articles.comments.delete', [1, 2]);
 * // -> '/articles/1/comments/2'
 * ```
 * or even with something as cursed as
 * ```
 * route('articles.comments.delete', [1, 'article' => 2]);
 * // -> '/articles/2/comments/1'
 * ```
 */
class UrlGenerationPathParameters
{
    /**
     * @param  list<string|null>  $names
     */
    public function __construct(public readonly array $names = [])
    {
    }

    public static function tryFromNode(Node $node): ?self
    {
        if (! ($node instanceof Node\Expr\Array_)) {
            return null;
        }

        $names = [];
        foreach ($node->items as $item) {
            $key = $item->key;
            if ($key === null) {
                $names[] = null;
            } else {
                $asString = Reflection::constantStringValueOf($key);
                if ($asString === null) {
                    return null;
                }

                $names[] = $asString;
            }
        }

        return new self($names);
    }

    /**
     * Returns the list of the contained parameters.
     *
     * This is mainly to handle the edge case of missing parameters that are
     * passed by index instead of by name. More info on that in the documetation
     * above this class.
     *
     * Note that the returned array might only return a true subset of all
     * required parameters. This helps to identify missing ones, which is now
     * possible, since the names of parameters passed by index are now named.
     * This is the major use of this function (and class).
     *
     * @param  list<string>  $correctOrder
     * @return array<string,string>
     *
     * @see UrlGenerator::replaceRouteParameters() for the detailed handling of
     * named and numeric array keys.
     */
    public function matchedTo(array $correctOrder): array
    {
        $matched = [];
        $correctIndex = array_flip($correctOrder);

        $passedByKey = [];
        foreach ($this->names as $name) {
            if ($name !== null) {
                $matched[$correctIndex[$name]] = $name;
                $passedByKey[] = $name;
            }
        }

        // This is one of the most important parts of this class. Here we
        // 'identify' parameters that were only passed by index!
        $passedByIndex = array_values(array_diff($correctOrder, $passedByKey));
        $numActuallyPassedByIndex = count($this->names) - count($passedByKey);
        for ($i = 0; $i < $numActuallyPassedByIndex; $i++) {
            $parameterName = array_shift($passedByIndex);

            // Now we need to find a "hole" where the parameter would be
            // inserted by Laravel's URL generator
            for ($index = 0; $index < count($this->names); $index++) {
                if (! array_key_exists($index, $matched)) {
                    $matched[$index] = $parameterName;
                    break;
                }
            }
        }

        return array_values($matched);
    }
}
