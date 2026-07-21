<x-mail::message>
# Welcome to {{ config('app.name') }}

Hi {{ $user->name }},

Your waiter account is ready.

**Sign in with:**
- **Name:** {{ $user->name }}
- **Default password:** {{ $defaultPassword }}

After you sign in, you’ll be asked to set a new password. Use this **email verification code** (from this Gmail) to confirm the change:

# {{ $changeCode }}

<x-mail::button :url="$loginUrl">
Sign in
</x-mail::button>

This code expires in 24 hours. Do not share it with anyone.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
