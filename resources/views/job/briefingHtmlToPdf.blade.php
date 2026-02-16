@inject('JobRegisterPresenter', 'App\Presenters\JobRegisterPresenter')

@php
/*
View variables:
===============
    - $Job: Job
*/

$headerBriefingB64 = base64_encode(file_get_contents(public_path('img/Header-Briefing-Ciclo.png')));
@endphp

<div style="font-family:barlowreg; font-size:14px;">
    <!-- header -->
    <div style="width:100%; text-align:center;">
        <table border="0" width="100%">
            <tr>
                <td width="100%" align="left" valign="middle">
                    <img width="2022" height="330" src="data:image/png;base64,{{$headerBriefingB64}}" />
                </td>
            </tr>
        </table>
    </div>

    <!-- client, job info -->
    <table border="0" width="100%">
        <tr>
            <td>
                <b>Cliente: {{ $Job?->client?->name }}</b>
            </td>
        </tr>
        <tr>
            <td>
                <b>Job: {{ $Job?->uid }} - {{ $Job?->title }}</b>
            </td>
        </tr>
    </table>

    <!-- objective -->
    @foreach ($JobRegisterPresenter::getJobBriefingFields($Job) as $field => $config)

        <br /><br />
        <table border="0" width="100%">
            <tr>
                <td colspan="3">
                    <b style="background-color:#fffede;">{{ $config['label'] }}</b>
                </td>
            </tr>
            <tr>
                <td width="1%"></td>
                <td width="98%">
                    {{ $Job?->briefing?->{$field} }}
                </td>
                <td width="1%"></td>
            </tr>
        </table>

    @endforeach
</div>
