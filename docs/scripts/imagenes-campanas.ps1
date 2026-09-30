# Genera las imágenes de las campañas (TP2-6) en images/campanas/.
#
# Son placas de 1200x675 con la paleta de DonaVida: degradado rojo, una gota
# y los datos de la campaña. Se generan con System.Drawing de .NET porque el
# PHP de XAMPP no trae GD activado. Las imágenes ya están en git: este script
# solo hace falta si cambian los datos de campanias.php.
#
# Uso, desde la carpeta del proyecto (Windows PowerShell):
#   powershell -ExecutionPolicy Bypass -File docs\scripts\imagenes-campanas.ps1

Add-Type -AssemblyName System.Drawing

$campanas = @(
    @{ Archivo = 'neuquen-centro'; Sede = 'Neuquén Centro'; Fecha = 'Sábado 17 de octubre'; Grupos = @('0-', '0+', 'A-') },
    @{ Archivo = 'cipolletti';     Sede = 'Cipolletti';     Fecha = 'Sábado 31 de octubre'; Grupos = @('B-', 'AB-') },
    @{ Archivo = 'plottier';       Sede = 'Plottier';       Fecha = 'Sábado 14 de noviembre'; Grupos = @('A+', '0+') },
    @{ Archivo = 'general-roca';   Sede = 'General Roca';   Fecha = 'Sábado 28 de noviembre'; Grupos = @('0-', 'A-', 'B-', 'AB-') },
    @{ Archivo = 'centenario';     Sede = 'Centenario';     Fecha = 'Sábado 12 de diciembre'; Grupos = @('0+', 'A+', 'B+') }
)

$ancho = 1200
$alto = 675
$rojo = [System.Drawing.Color]::FromArgb(176, 16, 24)
$rojoOscuro = [System.Drawing.Color]::FromArgb(125, 11, 17)
$destino = Join-Path (Split-Path -Parent (Split-Path -Parent $PSScriptRoot)) 'images\campanas'
New-Item -ItemType Directory -Force $destino | Out-Null

function Gota([float] $x, [float] $y, [float] $tam) {
    # Punta arriba y círculo abajo, como el logo
    $ruta = New-Object System.Drawing.Drawing2D.GraphicsPath
    $r = $tam / 2
    $ruta.AddLine($x, $y, $x + $r * 0.866, $y + $tam * 0.75)
    $ruta.AddArc($x - $r, $y + $tam * 0.5, $tam, $tam, -30, 240)
    $ruta.CloseFigure()
    return $ruta
}

foreach ($c in $campanas) {
    $bmp = New-Object System.Drawing.Bitmap $ancho, $alto
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.SmoothingMode = 'AntiAlias'
    $g.TextRenderingHint = 'AntiAliasGridFit'

    $fondo = New-Object System.Drawing.Drawing2D.LinearGradientBrush (New-Object System.Drawing.Point 0, 0), (New-Object System.Drawing.Point $ancho, $alto), $rojo, $rojoOscuro
    $g.FillRectangle($fondo, 0, 0, $ancho, $alto)

    # Gota grande de fondo, a la derecha
    $g.FillPath((New-Object System.Drawing.SolidBrush ([System.Drawing.Color]::FromArgb(40, 255, 255, 255))), (Gota 930 90 380))

    $blanco = [System.Drawing.Brushes]::White
    $suave = New-Object System.Drawing.SolidBrush ([System.Drawing.Color]::FromArgb(220, 255, 255, 255))
    $g.DrawString('CAMPAÑA DE DONACIÓN', (New-Object System.Drawing.Font 'Segoe UI Semibold', 26), $suave, 80, 110)
    $g.DrawString($c.Sede, (New-Object System.Drawing.Font 'Segoe UI', 80, ([System.Drawing.FontStyle]::Bold)), $blanco, 72, 160)
    $g.DrawString($c.Fecha + ' · 9 a 13 h', (New-Object System.Drawing.Font 'Segoe UI', 34), $blanco, 80, 300)

    # Grupos buscados, como etiquetas blancas
    $g.DrawString('Buscamos', (New-Object System.Drawing.Font 'Segoe UI Semibold', 24), $suave, 80, 440)
    $x = 80
    $fuenteGrupo = New-Object System.Drawing.Font 'Segoe UI', 30, ([System.Drawing.FontStyle]::Bold)
    $textoRojo = New-Object System.Drawing.SolidBrush $rojo

    foreach ($grupo in $c.Grupos) {
        $medida = $g.MeasureString($grupo, $fuenteGrupo)
        $w = [Math]::Max(96, $medida.Width + 36)
        $etiqueta = New-Object System.Drawing.Drawing2D.GraphicsPath
        $etiqueta.AddArc($x, 490, 36, 36, 180, 90)
        $etiqueta.AddArc($x + $w - 36, 490, 36, 36, 270, 90)
        $etiqueta.AddArc($x + $w - 36, 554, 36, 36, 0, 90)
        $etiqueta.AddArc($x, 554, 36, 36, 90, 90)
        $etiqueta.CloseFigure()
        $g.FillPath($blanco, $etiqueta)
        $g.DrawString($grupo, $fuenteGrupo, $textoRojo, $x + ($w - $medida.Width) / 2, 490 + (100 - $medida.Height) / 2)
        $x += $w + 20
    }

    $g.DrawString('DonaVida', (New-Object System.Drawing.Font 'Segoe UI Semibold', 24), $suave, 1030, 610)

    $archivo = Join-Path $destino ($c.Archivo + '.png')
    $bmp.Save($archivo, [System.Drawing.Imaging.ImageFormat]::Png)
    $g.Dispose()
    $bmp.Dispose()
    Write-Output "  creada       $archivo"
}
