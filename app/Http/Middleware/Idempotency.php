<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Idempotency
{
    /**
     * Seconds a completed response is replayed for an identical request.
     */
    public const REPLAY_TTL = 600;

    /**
     * Seconds a lock is held while an identical request is in flight.
     *
     * Kept short so a crashed request frees the lock quickly.
     */
    public const LOCK_TTL = 120;

    public function handle(Request $request, Closure $next)
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        $key = $this->key($request);

        $cached = Cache::get($key);

        if ($cached !== null) {
            return $this->replay($cached);
        }

        $lock = Cache::lock($key . ':lock', self::LOCK_TTL);

        if (! $lock->get()) {
            // An identical request is in flight: wait briefly for it to finish,
            // then serve exactly what it produced instead of running twice.
            for ($i = 0; $i < 10; $i++) {
                usleep(200000);
                $cached = Cache::get($key);

                if ($cached !== null) {
                    return $this->replay($cached);
                }
            }

            return back()->withErrors(['idempotency' => 'Your request is still being processed. Please try again in a moment.']);
        }

        try {
            // Re-check after winning the lock to close the last race window.
            $cached = Cache::get($key);

            if ($cached !== null) {
                return $this->replay($cached);
            }

            $response = $next($request);

            if ($response instanceof RedirectResponse && ! $request->session()->has('errors')) {
                Cache::put($key, $this->snapshot($request, $response), self::REPLAY_TTL);
            }

            return $response;
        } finally {
            $lock->release();
        }
    }

    private function key(Request $request): string
    {
        return 'idempotency:' . sha1(
            $request->method() . '|' . $request->fullUrl() . '|' . http_build_query($request->all()),
        );
    }

    private function snapshot(Request $request, RedirectResponse $response): array
    {
        $flashes = [];

        foreach (['success', 'warning', 'error', 'status'] as $bag) {
            if ($request->session()->has($bag)) {
                $value = $request->session()->get($bag);

                if (is_scalar($value) || is_string($value)) {
                    $flashes[$bag] = $value;
                }
            }
        }

        return [
            'url' => $response->getTargetUrl(),
            'flashes' => $flashes,
        ];
    }

    private function replay(array $snapshot): RedirectResponse
    {
        return redirect($snapshot['url'])->with($snapshot['flashes']);
    }
}