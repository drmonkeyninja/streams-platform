<?php namespace Anomaly\Streams\Platform\Http\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;

/**
 * Class HttpCache
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class HttpCache
{

    /**
     * The config repository.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Create a new PoweredBy instance.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Say it loud.
     *
     * @param  Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /* @var Response $response */
        $response = $next($request);

        /* @var Route $route */
        $route = $request->route();

        /**
         * Don't cache the admin.
         */
        if ($request->segment(1) == 'admin') {
            return $response;
        }

        /**
         * Don't cache if HTTP cache
         * is disabled in the route.
         */
        if ($route->getAction('streams::http_cache') === false) {
            return $response;
        }

        /**
         * Don't cache if HTTP cache
         * is disabled in the system.
         */
        if ($this->config->get('streams::httpcache.enabled', false) === false) {
            return $response;
        }

        /**
         * Don't let BOTs generate cache files.
         */
        if (!$this->config->get('streams::httpcache.allow_bots', false) === false) {
            return $response;
        }

        /**
         * Exclude these paths from caching
         * based on partial / exact URI.
         */
        $excluded = $this->config->get('streams::httpcache.excluded', []);

        if (is_string($excluded)) {
            $excluded = array_map(
                function ($line) {
                    return trim($line);
                },
                explode("\n", $excluded)
            );
        }

        foreach ((array)$excluded as $path) {
            if (str_is($path, $request->getPathInfo())) {
                return $response;
            }
        }

        /**
         * Define timeout rules based on
         * partial / exact URI matching.
         */
        $rules = $this->config->get('streams::httpcache.rules', []);

        if (is_string($rules)) {
            $rules = array_map(
                function ($line) {
                    return trim($line);
                },
                explode("\n", $rules)
            );
        }

        foreach ((array)$rules as $rule) {

            $parts = explode(' ', $rule);

            $path = array_shift($parts);
            $ttl  = array_shift($parts);

            if (str_is($path, $request->getPathInfo())) {
                $response->setTtl($ttl);
            }
        }

        /**
         * Set the TTL based on the original TTL or the route
         * action OR the config and lastly a default value.
         */
        if ($response->getTtl() === null) {
            $response->setTtl(
                $route->getAction('streams::http_cache') ?: $this->config->get('streams::httpcache.ttl', 3600)
            );
        }

        return $response;
    }

}
