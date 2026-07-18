<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerHasTable
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('customer_table')) {
            return redirect()
                ->route('customer.home')
                ->with('status', 'Please choose your table to continue.');
        }

        return $next($request);
    }
}
