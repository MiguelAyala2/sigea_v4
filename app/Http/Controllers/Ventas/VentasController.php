<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;

class VentasController extends Controller
{
    // Caja
    public function cajaApertura()
    {
        return view('ventas.caja.apertura');
    }

    public function cajaMovimientos()
    {
        return view('ventas.caja.movimientos');
    }

    public function cajaCierre()
    {
        return view('ventas.caja.cierre');
    }

    public function cajaArqueo()
    {
        return view('ventas.caja.arqueo');
    }

    public function cajaRecaudaciones()
    {
        return view('ventas.caja.recaudaciones');
    }

    // Pedidos de Clientes
    public function pedidosRegistrar()
    {
        return view('ventas.pedidos.registrar');
    }

    public function pedidosHistorial()
    {
        return view('ventas.pedidos.historial');
    }

    // Ventas y Facturación
    public function facturacion()
    {
        return view('ventas.facturacion.index');
    }

    public function cuentasCobrar()
    {
        return view('ventas.cuentas-cobrar.index');
    }

    public function remisiones()
    {
        return view('ventas.remisiones.index');
    }

    public function notasCredito()
    {
        return view('ventas.notas-credito.index');
    }

    public function notasDebito()
    {
        return view('ventas.notas-debito.index');
    }

    // Cobranzas
    public function cobranzasRegistrar()
    {
        return view('ventas.cobranzas.registrar');
    }

    public function cobranzasFormaPago()
    {
        return view('ventas.cobranzas.forma-pago');
    }

    public function cobranzasHistorial()
    {
        return view('ventas.cobranzas.historial');
    }

    // Libro de Ventas
    public function libroVentas()
    {
        return view('ventas.libro-ventas.index');
    }

    // Informes
    public function informes()
    {
        return view('ventas.informes.index');
    }
}
