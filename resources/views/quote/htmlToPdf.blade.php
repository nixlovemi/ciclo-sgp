@php
/*
View variables:
===============
    - $Quote: Quote
*/

$logoCicloB64 = base64_encode(file_get_contents(public_path('img/Logo-Ciclo.jpg')));
@endphp

<div style="font-family:barlowreg; font-size:14px;">
    <!-- header -->
    <div style="width:100%; background-color:#FFF500; text-align:center; font-weight:bold;">
        <table border="0" width="100%">
            <tr>
                <td width="10%" align="left" valign="middle">
                    <br /><br />
                    <img width="78" height="40" src="data:image/png;base64,{{$logoCicloB64}}" />
                </td>
                <td width="90%" align="center">
                    <br /><br />
                    <span style="font-size:28px; font-family:barlowextrabold; font-weight:bold;">Orçamento</span>
                </td>
            </tr>
        </table>
    </div>

    <br />

    <!-- data, cliente, ... -->
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="31%" align="left">
                <b>Data:</b> {{ $Quote?->formattedDate }}
            </td>
            <td width="5%" align="center">
                &nbsp;
            </td>
            <td width="64%" align="right">
                <b>Elaborado por:</b> {{ $Quote?->createUser?->name }}
            </td>
        </tr>

        <tr>
            <td align="left">
                <b>Validade:</b> {{ $Quote?->validity_days }} dias
            </td>
            <td>
                &nbsp;
            </td>
            <td align="right">
                <b>Cliente:</b> {{ $Quote?->client?->name }}
            </td>
        </tr>
    </table>

    <br /><br /><br />

    <!-- items -->
    <table border="1" width="100%" cellspacing="0" cellpadding="3">
        <tr>
            <td width="8%" align="center">
                <b>Qtde.</b>
            </td>
            <td width="8%" align="center">
                <b>Unid.</b>
            </td>
            <td width="54%" align="left">
                <b>Descrição</b>
            </td>
            <td width="15%" align="center">
                <b>Valor Unit.</b>
            </td>
            <td width="15%" align="center">
                <b>Valor Total</b>
            </td>
        </tr>

        @foreach ($Quote?->items as $item)
            <tr>
                <td align="center">
                    {{ $item?->quantity }}
                </td>
                <td align="center">
                    {{ $item?->type }}
                </td>
                <td align="left">
                    {{ $item?->serviceItem?->description }}
                </td>
                <td align="center">
                    {{ $item?->currencyPrice }}
                </td>
                <td align="center">
                    {{ $item?->currencyTotal }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td width="85%" align="right">
                <b>Total Geral</b>
            </td>
            <td width="15%" align="center">
                <b>{{ $Quote?->formattedTotal }}</b>
            </td>
        </tr>
    </table>

    <br /><br />

    <!-- payment type -->
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="10%" align="center">
                &nbsp;
            </td>
            <td width="90%" align="right">
                <b>Forma de Pagamento:</b> {{ $Quote?->payment_type }}
            </td>
        </tr>
    </table>

    <!-- payment type memo -->
    @if (!empty($Quote?->payment_type_memo))
        <br />

        <table border="1" width="100%" cellspacing="0" cellpadding="3">
            <tr>
                <td width="100%" align="center">
                    {{ $Quote?->payment_type_memo }}
                </td>
            </tr>
        </table>
    @endif

    @if (!empty($Quote?->notes))
        <br /><br />

        Comentários, Detalhes ou instruções especiais:
        <br />
        
        <table border="1" width="100%" cellspacing="0" cellpadding="6">
            <tr>
                <td width="100%" align="center">
                    {{ $Quote?->notes }}
                </td>
            </tr>
        </table>
    @endif
</div>