<tr>
<td class="header">
<a href="{{ config('app.url') }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('icon-512.png') }}" alt="Blade & Fade" width="40" height="40" style="display:block; margin:0 auto 10px; border-radius:8px;">
<span style="font-family:'Trebuchet MS', Verdana, sans-serif; font-size: 24px; font-weight: 700; letter-spacing:1px; color:#2158B8;">
BLADE <span style="color:#C79A3D;">&amp;</span> FADE
</span>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
