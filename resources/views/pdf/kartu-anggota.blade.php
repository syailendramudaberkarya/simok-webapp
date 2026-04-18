<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kartu Anggota Digital</title>
    <style>
        @page {
            margin: 0;
            size: 382.68pt 240.94pt;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color:
                {{ optional($template)->warna_utama ?? '#1e40af' }}
            ;
            color: #ffffff;
            width: 382.68pt;
            height: 240.94pt;
        }

        .outer-table {
            width: 100%;
            height: 230pt;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-cell {
            background: rgba(0, 0, 0, 0.15);
            padding: 8pt 20pt;
            height: 35pt;
        }

        .org-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1;
        }

        .org-sub {
            font-size: 8pt;
            opacity: 0.8;
            letter-spacing: 2pt;
            text-transform: uppercase;
        }

        .content-cell {
            padding: 5pt 20pt;
            vertical-align: middle;
        }

        .photo-box {
            width: 75pt;
            height: 95pt;
            border: 2pt solid #ffffff;
            background: white;
            overflow: hidden;
        }

        .label {
            font-size: 7.5pt;
            text-transform: uppercase;
            opacity: 0.7;
            margin-bottom: 2pt;
        }

        .member-name {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8pt;
            line-height: 1.1;
        }

        .member-id {
            font-size: 19pt;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.25);
            padding: 4pt 8pt;
            font-family: 'Courier-Bold', monospace;
        }

        .footer-cell {
            padding: 5pt 20pt;
            background: transparent;
            vertical-align: bottom;
            height: 45pt;
        }

        .footer-val {
            font-size: 10pt;
            font-weight: bold;
        }

        .qr-white-box {
            background: white;
            padding: 2.5pt;
            display: inline-block;
        }
    </style>
</head>

<body>
    @php
        $defaultPhoto = 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAQAAAAAYLlVAAAAOUlEQVR42u3OQQ0AAAgEoNP+nTWFDzzIQO0sqSUpEREREREREREREREREREREREREREREREREWkL97ICtXkFfXoAAAAASUVORK5CYII=';
        $photoSrc = "data:image/png;base64,{$defaultPhoto}";
        if ($anggota->foto_wajah_path) {
            $path = storage_path('app/private/' . $anggota->foto_wajah_path);
            if (file_exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $photoSrc = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($path));
            }
        }
        $qrCodeSrc = 'data:image/png;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(80)->margin(0)->generate($verifyUrl));
    @endphp

    <table class="outer-table" cellpadding="0" cellspacing="0">
        <!-- Header -->
        <tr>
            <td class="header-cell" colspan="2">
                <div class="org-name">SiMOK</div>
                <div class="org-sub">Sistem Manajemen Keanggotaan</div>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td class="content-cell" width="90pt" align="center">
                <div class="photo-box">
                    <img src="{{ $photoSrc }}" width="100%" height="100%">
                </div>
            </td>
            <td class="content-cell" style="padding-left: 5pt;">
                <div class="label">Nama Lengkap</div>
                <div class="member-name">{{ $anggota->nama_lengkap }}</div>

                <div class="label">Nomor Anggota</div>
                <div class="member-id">{{ $anggota->nomor_anggota }}</div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td class="footer-cell" colspan="2">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="35%" valign="bottom" style="padding-bottom: 5pt;">
                            <div class="label" style="font-size: 6pt; margin-bottom: 1pt;">Berlaku Sejak</div>
                            <div class="footer-val">
                                {{ $anggota->approved_at ? $anggota->approved_at->translatedFormat('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td width="35%" valign="bottom" style="padding-bottom: 5pt;">
                            <div class="label" style="font-size: 6pt; margin-bottom: 1pt;">Berlaku Hingga</div>
                            <div class="footer-val">
                                {{ $anggota->expired_at ? $anggota->expired_at->translatedFormat('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td align="right" valign="bottom">
                            <div class="qr-white-box">
                                <img src="{{ $qrCodeSrc }}" width="45" height="45">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>