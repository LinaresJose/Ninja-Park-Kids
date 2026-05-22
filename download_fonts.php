<?php
$fonts = [
    'inter' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap',
    'montserrat' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800;900&display=swap'
];

foreach ($fonts as $name => $url) {
    if (!is_dir(__DIR__."/public/fonts/$name")) {
        mkdir(__DIR__."/public/fonts/$name", 0777, true);
    }
    
    // Fetch CSS
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
        ]
    ]);
    
    $css = file_get_contents($url, false, $context);
    
    // Extract WOFF2 URLs
    preg_match_all('/url\((https:\/\/[^\)]+)\)/', $css, $matches);
    
    foreach ($matches[1] as $fontUrl) {
        $fileName = basename(parse_url($fontUrl, PHP_URL_PATH));
        echo "Downloading: $fileName\n";
        $fontData = file_get_contents($fontUrl);
        file_put_contents(__DIR__."/public/fonts/$name/$fileName", $fontData);
        // Replace URL in CSS
        $css = str_replace($fontUrl, "./$fileName", $css);
    }
    
    file_put_contents(__DIR__."/public/fonts/$name/$name.css", $css);
    echo "Saved $name.css\n";
}
echo "Fonts downloaded successfully!\n";
