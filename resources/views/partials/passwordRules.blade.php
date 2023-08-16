@inject('ValidatePassword', 'App\Helpers\ValidatePassword')

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-light bg-light text-dark border-0 m-0" role="alert">
            <strong>A senha deve conter:</strong>
            <br />
            <ul class="p-0 m-0">
                @foreach ($ValidatePassword::getRulesTexts() as $rulesText)
                    <li>- {{ $rulesText }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>