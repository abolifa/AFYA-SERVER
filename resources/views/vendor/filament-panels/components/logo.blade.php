@guest
    <div
        style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; margin-bottom: 20px;">
        <img
            src="{{ asset('/logo.png') }}"
            alt="Logo"
            style="width: 100px; height: auto"
        />
        <h1 style="font-size: 20px; font-weight: 900;">الهيئة الوطنية لأمراض الكلى</h1>
    </div>
@endguest
@auth
    <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
        <img
            src="{{ asset('/logo.png') }}"
            alt="Logo"
            style="width: 50px; height: auto;"
        />
        <h1 style="font-size: 18px; font-weight: 900; text-align: center;">نظام عافية</h1>
    </div>
@endauth
