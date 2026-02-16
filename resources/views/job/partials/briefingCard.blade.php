@inject('JobRegisterPresenter', 'App\Presenters\JobRegisterPresenter')

@php
/*
View variables:
===============
    - $disabled: bool
    - $Job: ?Job
*/
@endphp

@foreach ($JobRegisterPresenter::getJobBriefingFields($Job) as $field => $config)
    <div class="col-12">
        <div class="form-group mb-3">
            <label class="form-label">
                {{ $config['label'] }}
            </label>
            <textarea
                {{ !$disabled ?: 'disabled' }}
                class="form-control form-control-sm"
                name="{{ $config['name'] }}"
            >{{ $Job?->briefing?->{$field} }}</textarea>
        </div>
    </div>
@endforeach
