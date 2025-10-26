@component('mail::message')
{{-- Use HTML instead of Markdown for better control --}}
<h1 style="color: rgb(32, 26, 24); font-size: 24px; font-weight: bold; margin: 0 0 20px 0;">Password Reset Request</h1>

<p style="color: rgb(32, 26, 24); margin: 0 0 20px 0;">
    You are receiving this email because we received a password reset request for your account.
</p>

<h2 style="color: rgb(32, 26, 24); font-size: 18px; font-weight: bold; margin: 25px 0 15px 0;">Your Reset Code</h2>

<div class="highlight" style="background: rgb(255, 219, 205); color: rgb(54, 15, 0); padding: 20px; border-radius: 4px; text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0;">
    {{ $token }}
</div>

<p style="color: rgb(32, 26, 24); margin: 20px 0;">
    This reset code will expire in 60 minutes.
</p>

<p style="color: rgb(32, 26, 24); margin: 20px 0;">
    If you did not request a password reset, no further action is required.
</p>

<p style="color: rgb(32, 26, 24); margin: 30px 0 0 0;">
    Thanks,<br>
    HOA Montaña Team
</p>

@component('mail::subcopy')
If you're having trouble with the reset code, please contact our support team.
@endcomponent
@endcomponent
