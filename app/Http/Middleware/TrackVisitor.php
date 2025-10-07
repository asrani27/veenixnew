<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitor;
use Illuminate\Support\Facades\Log;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't track admin routes and certain file types
        if ($this->shouldTrack($request)) {
            try {
                Visitor::recordVisit($request);
            } catch (\Exception $e) {
                // Silently fail to not disrupt the user experience
                Log::error('Visitor tracking failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }

    /**
     * Determine if the request should be tracked
     */
    private function shouldTrack(Request $request): bool
    {
        // Don't track admin routes
        if ($request->is('admin/*') || $request->is('dashboard')) {
            return false;
        }

        // Don't track certain file extensions
        $extensions = ['css', 'js', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf'];
        $path = $request->path();
        
        foreach ($extensions as $ext) {
            if (str_ends_with($path, '.' . $ext)) {
                return false;
            }
        }

        // Don't track AJAX requests or certain user agents
        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        // Don't track bots
        $userAgent = $request->userAgent();
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
            'facebookexternalhit', 'twitterbot', 'linkedinbot', 'whatsapp',
            'slackbot', 'telegram', 'googlebot', 'bingbot', 'yandexbot'
        ];

        if ($userAgent) {
            foreach ($botPatterns as $pattern) {
                if (stripos($userAgent, $pattern) !== false) {
                    return false;
                }
            }
        }

        return true;
    }
}
