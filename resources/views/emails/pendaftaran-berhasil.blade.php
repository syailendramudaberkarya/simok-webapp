<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Berhasil</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 30px; text-align: center;">
                <h1 style="color: white; margin: 0; font-size: 24px;">Pendaftaran Diterima!</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0; font-size: 14px;">Sistem Informasi Manajemen Organisasi Keanggotaan</p>
            </div>

            <!-- Content -->
            <div style="padding: 30px;">
                <p>Halo, <strong>{{ $user->name }}</strong>,</p>
                
                <p>Terima kasih telah mendaftar sebagai anggota di SiMOK. Data pendaftaran Anda berhasil kami terima.</p>
                
                <!-- Status -->
                <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 0; font-weight: bold; color: #92400e;">⏳ Menunggu Verifikasi</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #78350f;">Status keanggotaan Anda saat ini sedang menunggu peninjauan oleh Administrator. Anda akan menerima email pemberitahuan setelah data selesai ditinjau.</p>
                </div>

                <h3 style="border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; color: #374151;">Data Akun Anda</h3>
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280; width: 100px;">Nama</td>
                        <td style="padding: 6px 0; font-weight: bold;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6b7280;">Email</td>
                        <td style="padding: 6px 0; font-weight: bold;">{{ $user->email }}</td>
                    </tr>
                </table>
                
                <p style="font-size: 13px; color: #6b7280; margin-top: 20px;">Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.</p>
            </div>

            <!-- Footer -->
            <div style="background: #f9fafb; padding: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                    Salam hormat,<br>
                    Tim Administrator SiMOK
                </p>
            </div>
        </div>
    </div>
</body>
</html>
