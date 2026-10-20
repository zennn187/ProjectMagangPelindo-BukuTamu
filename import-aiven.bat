@echo off
REM Import database/buku_tamu_export.sql ke Aiven MySQL
REM Cara pakai: edit PASSWORD dan CA_PATH di bawah, lalu dobel-klik file ini.
REM Jangan jalankan dari PowerShell langsung — file .bat otomatis pakai CMD (dukung operator ^<).

set DB_HOST=mysql-37bc1d-bukutamu-pelindo.b.aivencloud.com
set DB_PORT=13192
set DB_USER=avnadmin
set DB_NAME=buku_tamu

REM ==== EDIT DUA BARIS INI ====
set DB_PASSWORD=
REM Isi manual tiap run: set DB_PASSWORD=password_asli  (jangan commit password!)
REM Cari ca.pem di beberapa lokasi umum (folder project dulu — sudah saya isikan)
set CA_PATH=%~dp0database\aiven-ca.pem
if not exist "%CA_PATH%" set CA_PATH=D:\Downloads\ca.pem
if not exist "%CA_PATH%" set CA_PATH=D:\ca.pem
if not exist "%CA_PATH%" set CA_PATH=%USERPROFILE%\Downloads\ca.pem
REM =============================

if not exist "%CA_PATH%" (
  echo [INFO] File CA tidak ketemu di %CA_PATH%
  echo Download dulu dari Aiven: service -^> Overview -^> CA certificate -^> Download,
  echo simpan sebagai ca.pem di folder Downloads, lalu jalankan lagi.
  pause
  exit /b 1
)

echo Mengimpor ke %DB_HOST%:%DB_PORT%/%DB_NAME% ...
REM Client mysql XAMPP lawas: tidak kenal caching_sha2_plugin modern maupun
REM flag --ssl-mode. Opsi paling kompatibel: --ssl-ca saja (mengaktifkan SSL
REM + verifikasi CA). Tanpa --ssl-ca koneksi akan plaintext dan ditolak Aiven.
C:\xampp\mysql\bin\mysql.exe -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASSWORD% %DB_NAME% --ssl-ca="%CA_PATH%" < "%~dp0database\buku_tamu_export.sql"

if errorlevel 1 (
  echo.
  echo [GAGAL] Cek password / host / port / file CA.
  pause
  exit /b 1
)

echo.
echo [SUKSES] Import selesai. Verifikasi:
C:\xampp\mysql\bin\mysql.exe -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASSWORD% %DB_NAME% --ssl-ca="%CA_PATH%" -e "SELECT COUNT(*) AS visits FROM visits; SELECT COUNT(*) AS users FROM users;"
pause
