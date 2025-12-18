<!-- {{-- Encabezado con logos --}} -->
<table class="tabla-encabezado" style="margin-bottom: 10px;">
    <tr>
        <td style="width: 20%; vertical-align: middle;">
            <img src="{{ $logo_izq }}" class="encabezado-logo" style="max-height: 50px;">
        </td>
        <td style="width: 60%; text-align: center; vertical-align: middle; font-size: 10px; line-height: 1.3;">
            <strong style="font-size: 13px; font-weight: bold;">SIGEA S.A. - AGUATERÍA Y PLOMERÍA</strong><br>
            <span style="font-size: 9px;">ventas@aguateriasigea.com.py | aguateriasigea.com.py | Contacto: 021 555-124 | Av. Eusebio Ayala Km 4.5</span><br>
            @hassection('departamento')
                @yield('departamento')<br>
            @endif
            <small style="font-size: 8px; color: #666;">Generado el: {{ date('d/m/Y H:i') }} Hs</small>
        </td>
        <td style="width: 20%; vertical-align: middle;">
            <img src="{{ $logo_der }}" class="encabezado-logo" style="max-height: 50px;">
        </td>
    </tr>
</table>
