<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:#f3f6f9;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:16px 8px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:520px; background:#ffffff; border-radius:10px; padding:24px;">

                <tr>
                    <td align="center" style="font-size:22px; font-weight:700; color:#154D71;">
                        🔐 Permintaan Perubahan Kata Sandi<br>
                        TALLY HBT
                    </td>
                </tr>

                <tr><td height="16"></td></tr>

                <tr>
                    <td align="center" style="font-size:15px; color:#333; line-height:1.6;">
                        Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.<br>
                        Silakan klik tombol di bawah ini untuk membuat kata sandi baru.
                    </td>
                </tr>

                <tr><td height="24"></td></tr>

                <tr>
                    <td align="center">
                        <a href="{{ $actionUrl }}"
                           style="background:#154D71; color:#ffffff;
                                  text-decoration:none; padding:12px 24px;
                                  border-radius:6px; display:inline-block;
                                  font-size:15px; font-weight:600;">
                            {{ $actionText ?? 'Atur Ulang Kata Sandi' }}
                        </a>
                    </td>
                </tr>

                <tr><td height="24"></td></tr>

                <tr>
                    <td align="center" style="font-size:14px; color:#555;">
                        Jika Anda tidak merasa melakukan permintaan reset kata sandi,
                        Anda dapat mengabaikan email ini.
                    </td>
                </tr>

                <tr><td height="16"></td></tr>

                <tr>
                    <td align="center"
                        style="border-top:1px solid #e5e7eb;
                               padding-top:12px;
                               font-size:12px; color:#888;">
                        © {{ date('Y') }} <strong>TALLY HBT</strong>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
