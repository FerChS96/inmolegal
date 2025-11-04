<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Arrendamiento</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            padding: 20px;
            text-align: justify;
            position: relative;
        }
        .logo-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-header img {
            max-width: 200px;
            height: auto;
        }
        h1 {
            color: #333;
            text-align: center;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }
        .titulo-seccion {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .no-break-section {
            page-break-inside: avoid;
        }
        .firmas-container {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 50px;
        }
        .firma {
            text-align: center;
            width: 30%;
            display: inline-block;
        }
        .firma-nombre {
            padding-top: 5px;
            margin-top: 80px;
            border-top: 1px solid #333;
        }
        .footer {
            position: fixed;
            bottom: 5px;
            left: 0;
            right: 0;
            font-size: 10px;
            color: #666;
            width: 100%;
            text-align: center;
        }
        p {
            margin-bottom: 10px;
        }
        b {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="logo-header">
        <img src="{{ public_path('images/logo-inmolegal.svg') }}" alt="InmoLegal">
    </div>
    
    <p><b>CONTRATO DE ARRENDAMIENTO DE INMUEBLE</b> que celebran de una parte <b>{{ strtoupper($contrato->nombres_arrendador . ' ' . $contrato->apellido_paterno_arrendador . ' ' . $contrato->apellido_materno_arrendador) }}</b> con CURP <b>{{ strtoupper($contrato->curp_arrendador) }}</b> en su carácter de <b>EL ARRENDADOR</b> y por la otra, como <b>EL ARRENDATARIO</b>, <b>{{ strtoupper($contrato->nombres_arrendatario . ' ' . $contrato->apellido_paterno_arrendatario . ' ' . $contrato->apellido_materno_arrendatario) }}</b> con CURP <b>{{ strtoupper($contrato->curp_arrendatario) }}</b>@if($contrato->tiene_fiador), quien cuenta con <b>OBLIGADO SOLIDARIO</b> <b>{{ strtoupper($contrato->nombres_fiador . ' ' . $contrato->apellido_paterno_fiador . ' ' . $contrato->apellido_materno_fiador) }}</b> con CURP <b>{{ strtoupper($contrato->curp_fiador) }}</b>@endif, mexicanos mayores de edad, hábiles para contratar y obligarse, y dijeron que es su voluntad celebrar el presente contrato de arrendamiento conforme a lo establecido por las disposiciones enmarcadas dentro del Código Civil Federal y sus correlativos en el Código Civil para {{ strtoupper($contrato->codigo_estado) }}, mismo que sujetan a las siguientes:</p>

    <p class="titulo-seccion">D E C L A R A C I O N E S.</p>

    <p><b>I.</b> Manifiesta <b>EL ARRENDADOR</b>, que cuenta con la capacidad legal necesaria para dar en arrendamiento <b>EL INMUEBLE</b> ubicado en <b>{{ strtoupper($contrato->calle) }} #{{ $contrato->numero_exterior }}@if($contrato->numero_interior) INT. {{ $contrato->numero_interior }}@endif, COLONIA {{ strtoupper($contrato->colonia) }}, C.P. {{ $contrato->codigo_postal }}, {{ strtoupper($contrato->ciudad) }}, {{ strtoupper($contrato->codigo_estado) }}</b>.</p>

    <p><b>II.</b> Que dicho inmueble no presenta vicios o defectos y por lo tanto se encuentra en buenas condiciones para utilizarse para <b>HABITACIÓN</b>.</p>

    <p><b>III.</b> Manifiesta <b>EL ARRENDATARIO</b> que cuenta con la capacidad legal necesaria para recibir en arrendamiento el inmueble descrito con anterioridad.</p>

    <p><b>IV.</b> Expresan ambas partes su conformidad con las mencionadas declaraciones, y deciden celebrar este contrato de arrendamiento bajo las siguientes:</p>

    <p class="titulo-seccion">C L Á U S U L A S.</p>

    <p><b>Primera.</b> El presente contrato se otorga respecto de <b>EL INMUEBLE</b> ubicado en <b>{{ strtoupper($contrato->calle) }} #{{ $contrato->numero_exterior }}@if($contrato->numero_interior) INT. {{ $contrato->numero_interior }}@endif, COLONIA {{ strtoupper($contrato->colonia) }}, C.P. {{ $contrato->codigo_postal }}, {{ strtoupper($contrato->ciudad) }}, {{ strtoupper($contrato->codigo_estado) }}</b>, conviniendo ambas partes que el inmueble se destinará exclusivamente para <b>HABITACIÓN</b>.</p>

    <p><b>Segunda.</b> Las partes convienen en que el precio del arrendamiento es la cantidad de <b>${{ number_format($contrato->precio_mensual, 2) }} ({{ numero_a_letras($contrato->precio_mensual) }} PESOS 00/100 M.N.)</b> mensuales, dicho pago deberá hacerlo <b>EL ARRENDATARIO</b> en <b>{{ strtoupper($contrato->forma_pago) }}</b>, por adelantado cada mes iniciando en la fecha en que se firma este instrumento, en <b>{{ strtoupper($contrato->cuenta_domicilio) }}</b>, hasta en tanto <b>EL ARRENDADOR</b> no le notifique por escrito que deba realizarlo en diversa forma, y/o domicilio, de acuerdo con lo que previene el artículo 2427 del Código Civil Federal y sus correlativos en el Código Civil para {{ strtoupper($contrato->codigo_estado) }}.</p>

    <p><b>Tercera.</b> <b>EL ARRENDADOR</b> da en arrendamiento a <b>EL ARRENDATARIO</b>, quien recibe a su entera satisfacción, el inmueble, descrito en la cláusula primera de este contrato para destinarlo exclusivamente para <b>HABITACIÓN</b>, por lo que <b>EL ARRENDATARIO</b> no podrá hacer otro uso del inmueble arrendado, solo utilizarlo para lo establecido en este instrumento, so pena de incurrir en causal de rescisión, y de hacerse acreedor a las penas convencionales que más adelante se estipulan.</p>

    <p><b>Cuarta.</b> El término del arrendamiento será de <b>{{ $contrato->plazo_meses }} MESES</b> forzosos para ambas partes y que correrá desde el {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d') }} de {{ strtoupper(\Carbon\Carbon::parse($contrato->fecha_inicio)->locale('es')->monthName) }} del {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('Y') }}, y si <b>EL ARRENDATARIO</b> quisiera seguir rentando el inmueble objeto de este contrato deberá avisar como mínimo 30 treinta días antes de la terminación del presente, con el objeto de firmar un nuevo contrato. <b>EL ARRENDATARIO</b> deberá pagar el precio de la mensualidad completa aún cuando ocupe el inmueble arrendado por una fracción del mes.</p>

    <p><b>Quinta.</b> Queda expresamente prohibido traspasar o subarrendar todo o parte del bien arrendado, siendo tal subarrendamiento causal de rescisión del presente contrato tal y como lo previene el artículo 2480 del Código Civil Federal y sus correlativos en el Código Civil para {{ strtoupper($contrato->codigo_estado) }}.</p>

    <p><b>Sexta.</b> <b>EL ARRENDATARIO</b> declara haber recibido el inmueble arrendado y sus accesorios, en perfecto estado, para utilizarlo para <b>HABITACIÓN</b>, de conformidad con los artículos 2442 y 2443 del Código Civil Federal y sus correlativos en el Código Civil para {{ strtoupper($contrato->codigo_estado) }}.</p>

    <p><b>Séptima.</b> Queda expresamente convenido que cualquier mejora que hiciera <b>EL ARRENDATARIO</b> en la localidad arrendada queda a beneficio de la finca y que para hacerlos requerirá previamente el permiso por escrito de <b>EL ARRENDADOR</b>, sin que en ningún caso la realización de tales mejoras pueda dar lugar a exigir su pago a <b>EL ARRENDADOR</b>.</p>

    <p><b>Octava.</b> <b>EL ARRENDATARIO</b> no podrá retener ni descontar de la renta cantidad alguna por concepto de mejoras o reparaciones, aunque las hubiera hecho por orden de alguna autoridad, pues en caso de recibir tal orden deberá de avisar inmediatamente a <b>EL ARRENDADOR</b>, para lo cual renuncia a los derechos que pudieran favorecerle.</p>

    <p><b>Novena. Cláusula especial de extinción de dominio.</b> <b>EL ARRENDADOR</b> manifiesta que el inmueble dado en arrendamiento a <b>EL ARRENDATARIO</b> será utilizado para el fin pactado en este contrato, uso lícito de acuerdo con la ley, por lo que en caso de que el inquilino realice cualquier acción que contravenga las disposiciones legales, será su única y absoluta responsabilidad, liberando desde este momento a <b>EL ARRENDADOR</b> de toda responsabilidad por tal concepto. El inmueble objeto de este contrato no cae dentro de los supuestos señalados en la ley federal de extinción de dominio, quedando dichos actos bajo la estricta responsabilidad de <b>EL ARRENDATARIO</b>.</p>

    <p><b>EL ARRENDATARIO</b> no podrá realizar actos que vayan en contra de las leyes mexicanas, el incumplimiento de este acuerdo será una causa especial para dar por terminado este convenio y pedir la devolución del inmueble, así mismo libera expresamente a <b>EL ARRENDADOR</b> de cualquier responsabilidad civil, penal o de cualquier otro tipo en que pudiera incurrir <b>EL ARRENDATARIO</b> al interior del inmueble objeto de este contrato. Igualmente queda prohibido a <b>EL ARRENDATARIO</b> realizar cualquier tipo de conducta que ponga en riesgo a quienes asistan al inmueble, así como escandalizar y realizar cualquier conducta que moleste a los vecinos, no permita el descanso, altere la paz y sana convivencia o que de cualquier forma incomode a los visitantes, sus asistentes y vecinos.</p>

    <p><b>Décima.</b> <b>EL ARRENDATARIO</b> no podrá tener substancias corrosivas, materiales inflamables o peligrosos, o algún material de desecho que perjudique la localidad arrendada.</p>

    <p><b>Décima primera.</b> Para garantizar el cumplimiento del presente contrato <b>EL ARRENDATARIO</b> deja un depósito por la cantidad de <b>${{ number_format($contrato->precio_mensual, 2) }} ({{ numero_a_letras($contrato->precio_mensual) }} PESOS 00/100 M.N.)</b>, el cual se devolverá a <b>EL ARRENDATARIO</b> una vez que se haya dado debido cumplimiento al presente y entregado el inmueble arrendado a <b>EL ARRENDADOR</b> en las condiciones que se le entregó y sin adeudo alguno por cualquier concepto.</p>

@if($contrato->tiene_fiador)
    <p><b>Décima segunda. Cláusula de Obligado Solidario.</b> <b>{{ strtoupper($contrato->nombres_fiador . ' ' . $contrato->apellido_paterno_fiador . ' ' . $contrato->apellido_materno_fiador) }}</b> con CURP <b>{{ strtoupper($contrato->curp_fiador) }}</b> se constituye como <b>OBLIGADO SOLIDARIO</b> de <b>EL ARRENDATARIO</b> en el cumplimiento de todas y cada una de las obligaciones derivadas del presente contrato, obligándose de manera solidaria e ilimitada al pago de las rentas, así como de cualquier otro concepto derivado de este contrato de arrendamiento, conforme a lo dispuesto por los artículos 1987 y siguientes del Código Civil Federal y sus correlativos en el Código Civil para {{ strtoupper($contrato->codigo_estado) }}.</p>

    <p><b>Décima tercera.</b> Las partes contratantes están perfectamente enteradas del contenido y alcance de todas y cada una de las cláusulas anteriores, y se someten expresamente a las leyes y tribunales del Estado de {{ strtoupper($contrato->codigo_estado) }}, haciendo renuncia expresa a cualquier otro fuero que pudiera corresponderles por razón de su domicilio presente o futuro.</p>
@else
    <p><b>Décima segunda.</b> Las partes contratantes están perfectamente enteradas del contenido y alcance de todas y cada una de las cláusulas anteriores, y se someten expresamente a las leyes y tribunales del Estado de {{ strtoupper($contrato->codigo_estado) }}, haciendo renuncia expresa a cualquier otro fuero que pudiera corresponderles por razón de su domicilio presente o futuro.</p>
@endif

<div class="no-break-section">
    <p>Enterados del contenido y alcance legal del presente instrumento, los contratantes lo firman en {{ strtoupper($contrato->codigo_estado) }} el día {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d') }} de {{ strtoupper(\Carbon\Carbon::parse($contrato->fecha_inicio)->locale('es')->monthName) }} del {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('Y') }}.</p>

    <div class="firmas-container">
        <div class="firma">
            <p>EL ARRENDADOR</p>
            <p class="firma-nombre"><b>{{ strtoupper($contrato->nombres_arrendador . ' ' . $contrato->apellido_paterno_arrendador . ' ' . $contrato->apellido_materno_arrendador) }}</b></p>
        </div>
        <div class="firma">
            <p>EL ARRENDATARIO</p>
            <p class="firma-nombre"><b>{{ strtoupper($contrato->nombres_arrendatario . ' ' . $contrato->apellido_paterno_arrendatario . ' ' . $contrato->apellido_materno_arrendatario) }}</b></p>   
        </div>
        @if($contrato->tiene_fiador)
        <div class="firma">
            <p>OBLIGADO SOLIDARIO</p>
            <p class="firma-nombre"><b>{{ strtoupper($contrato->nombres_fiador . ' ' . $contrato->apellido_paterno_fiador . ' ' . $contrato->apellido_materno_fiador) }}</b></p>
        </div>
        @endif
    </div>
</div>

<div class="footer">
    Documento generado automáticamente por InmoLegal. Para más información visita www.inmolegalmx.com. Token: {{ $contrato->token }}
</div>
</body>
</html>
