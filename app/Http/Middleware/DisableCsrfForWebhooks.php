namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCsrfForWebhooks
{
    public function handle(Request $request, Closure $next)
    {
        // Bypass CSRF only for this route
        if ($request->is('webhook/wordpress-form')) {
            return $next($request);
        }

        return abort(403, 'CSRF protection enabled');
    }
}
