<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al panel
        if (session('admin_authenticated')) {
            return redirect()->route('admin.panel');
        }
        
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        // Contraseña hardcodeada - cambiar en producción
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');

        if ($request->password === $adminPassword) {
            session(['admin_authenticated' => true]);
            return redirect()->route('admin.panel');
        }

        return back()->withErrors(['password' => 'Contraseña incorrecta'])->withInput();
    }

    public function logout()
    {
        session()->forget('admin_authenticated');
        return redirect()->route('admin.login');
    }

    public function panel()
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        // Obtener estadísticas generales
        $totalContratos = Contrato::count();
        $contratosPagados = Contrato::whereNotNull('fecha_pago')->count();
        $totalPagos = Pago::where('status', 'paid')->count();
        $montoTotal = Contrato::whereNotNull('monto_pagado')->sum('monto_pagado');

        return view('admin.panel', compact(
            'totalContratos',
            'contratosPagados',
            'totalPagos',
            'montoTotal'
        ));
    }

    public function contratos(Request $request)
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $query = Contrato::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('token', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('nombres_arrendatario', 'LIKE', "%{$search}%")
                  ->orWhere('apellido_paterno_arrendatario', 'LIKE', "%{$search}%")
                  ->orWhere('curp_arrendatario', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('estado_pago')) {
            if ($request->estado_pago === 'pagado') {
                $query->whereNotNull('fecha_pago');
            } else {
                $query->whereNull('fecha_pago');
            }
        }

        // Ordenar por más reciente
        $contratos = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.contratos', compact('contratos'));
    }

    public function pagos(Request $request)
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $query = Pago::with('contrato');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_request_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('contrato', function($q2) use ($search) {
                      $q2->where('token', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar por más reciente
        $pagos = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pagos', compact('pagos'));
    }

    public function verContrato($token)
    {
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }

        $contrato = Contrato::where('token', $token)->firstOrFail();
        $pagos = Pago::where('idcontrato', $contrato->idcontrato)->get();

        return view('admin.ver-contrato', compact('contrato', 'pagos'));
    }
}
