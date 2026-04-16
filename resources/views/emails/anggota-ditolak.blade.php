<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Ditolak</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #991b1b, #dc2626); padding: 30px; text-align: center;">
                <h1 style="color: white; margin: 0; font-size: 24px;">Pemberitahuan Pendaftaran</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0; font-size: 14px;">Sistem Informasi Manajemen Organisasi Keanggotaan</p>
            </div>

            <!-- Content -->
            <div style="padding: 30px;">
                <p>Halo, <strong>{{ $anggota->nama_lengkap }}</strong>,</p>
                
                <p>Terima kasih atas partisipasi Anda mendaftar di SiMOK.</p>
                
                <p>Setelah melakukan peninjauan terhadap data dan dokumen yang Anda lampirkan, dengan berat hati kami sampaikan bahwa pendaftaran Anda <strong>DITOLAK</strong>.</p>
                
                <!-- Alasan Penolakan -->
                <div style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 0; font-weight: bold; color: #991b1b;">Alasan Penolakan:</p>
                    <p style="margin: 8px 0 0 0; color: #7f1d1d;">{{ $alasan }}</p>
                </div>
                
                <p>Jika Anda merasa ini adalah sebuah kesalahan atau ingin memperbaiki data Anda, silakan hubungi tim administrator kami untuk informasi lebih lanjut.</p>
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
