<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Disetujui</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 30px; text-align: center;">
                <h1 style="color: white; margin: 0; font-size: 24px;">🎉 Selamat!</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0; font-size: 14px;">Keanggotaan Anda Telah Disetujui</p>
            </div>

            <!-- Content -->
            <div style="padding: 30px;">
                <p>Halo, <strong>{{ $anggota->nama_lengkap }}</strong>,</p>
                
                <p>Kami dengan senang hati memberitahukan bahwa pendaftaran Anda di Sistem Informasi Manajemen Organisasi Keanggotaan (SiMOK) telah berhasil <strong>DISETUJUI</strong> oleh Administrator.</p>
                
                <!-- Nomor Anggota -->
                <div style="background: #eff6ff; border: 2px solid #bfdbfe; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px;">Nomor Anggota Anda</p>
                    <h2 style="margin: 8px 0 0 0; color: #1e40af; font-size: 32px; font-family: 'Courier New', monospace; letter-spacing: 3px;">{{ $anggota->nomor_anggota }}</h2>
                </div>

                <!-- Info Kartu -->
                <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 0; font-weight: bold; color: #166534;">✅ Kartu Anggota Digital Siap</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #15803d;">Kartu Tanda Anggota Digital Anda telah berhasil digenerate dan siap diunduh melalui Dashboard Anggota.</p>
                </div>

                @if(!empty($password))
                <!-- Info Login -->
                <div style="background: #fdf2f8; border-left: 4px solid #db2777; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 0; font-weight: bold; color: #9d174d;">🔐 Informasi Login Anda</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #831843;">Anda telah didaftarkan oleh Administrator. Berikut adalah detail login Anda:</p>
                    <ul style="margin: 5px 0 0 0; font-size: 14px; color: #831843;">
                        <li>Username: <strong>{{ $anggota->user->username ?? '-' }}</strong></li>
                        <li>Email: <strong>{{ $anggota->user->email ?? '-' }}</strong></li>
                        <li>Password: <strong>{{ $password }}</strong></li>
                    </ul>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #be185d;">Disarankan untuk segera mengubah password Anda setelah berhasil login.</p>
                </div>
                @endif

                <!-- CTA Button -->
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('login') }}" style="background: #1e40af; color: white; padding: 14px 36px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block;">Masuk ke Dashboard</a>
                </div>

                <p style="font-size: 13px; color: #6b7280;">Setelah login, navigasikan ke menu <strong>Kartu Anggota</strong> pada sidebar untuk melihat dan mengunduh kartu digital Anda dalam format PDF atau PNG.</p>
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
