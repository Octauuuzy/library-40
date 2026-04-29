<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika bukan admin (termasuk user anggota atau guest/tanpa akun)
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            
            // Block akses data modifying (POST, PUT, DELETE) agar exploiter tidak bisa merubah data
            if (!$request->isMethod('get')) {
                return abort(403, 'Akses Ditolak: Hanya Administrator yang berhak memiliki akses ini.');
            }
            
            // Untuk GET request, render UI Dashboard kosong tanpa data dan query
            return response(view('dashboard.empty'));
        }

        return $next($request);
    }
}
