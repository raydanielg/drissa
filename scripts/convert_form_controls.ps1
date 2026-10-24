$ErrorActionPreference = 'Stop'
$root = 'resources\views'
$files = Get-ChildItem -Recurse $root -Filter *.blade.php | Where-Object { $_.FullName -notmatch 'components[\\/]ui[\\/]' }

# Protect <script> blocks, blade comments, and <template> from rewriting
$protectRx = [regex]::new('(<script\b(?:"[^"]*"|''[^'']*''|[^''">])*>.*?</script>)|(\{\{--.*?--\}\})|(<template\b.*?</template>)', 'Singleline')

# input types to convert to <x-ui.input>
$okTypes = 'text','email','password','number','date','datetime-local','time','tel','url','search','file','month','week'

$inputRx  = [regex]::new('<input\b((?:"[^"]*"|''[^'']*''|[^''">])*)/?>', 'Singleline')
$selRx    = [regex]::new('<(/?)select\b((?:"[^"]*"|' + "'" + '[^' + "'" + ']*' + "'" + '|[^' + "'" + '">])*)>', 'Singleline')
$taRx     = [regex]::new('<(/?)textarea\b((?:"[^"]*"|' + "'" + '[^' + "'" + ']*' + "'" + '|[^' + "'" + '">])*)>', 'Singleline')

$inCount = 0; $selCount = 0; $taCount = 0; $fpCount = 0

foreach ($f in $files) {
    $content = [IO.File]::ReadAllText($f.FullName)
    $original = $content
    $protected = @{}
    $idx = 0

    # stash protected regions
    $content = $protectRx.Replace($content, {
        param($m)
        $key = "@@PROT$($idx++)@@"
        $protected[$key] = $m.Value
        return $key
    })

    # --- <input> -> <x-ui.input> ---
    $content = $inputRx.Replace($content, {
        param($m)
        $attrs = $m.Groups[1].Value
        $tm = [regex]::Match($attrs, '\btype\s*=\s*"([^"]*)"')
        if (-not $tm.Success) { $tm = [regex]::Match($attrs, "\btype\s*=\s*'([^']*)'") }
        $type = if ($tm.Success) { $tm.Groups[1].Value.ToLower() } else { 'text' }
        # skip blade-templated / js / non-text types
        if ($type -match '\{\{|\$' -or $okTypes -notcontains $type) { return $m.Value }

        # date types -> flatpickr markers, type becomes text
        if ($type -eq 'date')           { $attrs = $attrs -replace '\btype\s*=\s*"date"', 'type="text" data-fp-date' ; $script:fpCount++ }
        elseif ($type -eq 'datetime-local') { $attrs = $attrs -replace '\btype\s*=\s*"datetime-local"', 'type="text" data-fp-datetime' ; $script:fpCount++ }
        elseif ($type -eq 'time')       { $attrs = $attrs -replace '\btype\s*=\s*"time"', 'type="text" data-fp-time' ; $script:fpCount++ }

        $script:inCount++
        return '<x-ui.input' + $attrs + ' />'
    })

    # --- <select> -> <x-ui.select> ---
    $content = $selRx.Replace($content, {
        param($m)
        $close = $m.Groups[1].Value; $attrs = $m.Groups[2].Value
        if ($attrs -match '\{\{|\$\{') { return $m.Value }
        $script:selCount++
        return '<' + $close + 'x-ui.select' + $attrs + '>'
    })

    # --- <textarea> -> <x-ui.textarea> ---
    $content = $taRx.Replace($content, {
        param($m)
        $close = $m.Groups[1].Value; $attrs = $m.Groups[2].Value
        if ($attrs -match '\{\{|\$\{') { return $m.Value }
        $script:taCount++
        return '<' + $close + 'x-ui.textarea' + $attrs + '>'
    })

    # restore protected regions
    foreach ($k in $protected.Keys) { $content = $content.Replace($k, $protected[$k]) }

    if ($content -ne $original) { [IO.File]::WriteAllText($f.FullName, $content) }
}

Write-Output "inputs->x-ui.input: $inCount   selects->x-ui.select: $selCount   textareas->x-ui.textarea: $taCount   flatpickr-marked: $fpCount"
