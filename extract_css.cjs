const fs = require('fs');
const path = require('path');

const appBladePath = path.join(__dirname, 'resources/views/diseños/app.blade.php');
const staffBladePath = path.join(__dirname, 'resources/views/diseños/staff.blade.php');
const appCssPath = path.join(__dirname, 'resources/css/app.css');

function extractStyles(filePath) {
    if (!fs.existsSync(filePath)) return '';
    let content = fs.readFileSync(filePath, 'utf8');
    
    let extractedCss = [];
    
    // Replace all <style> blocks
    content = content.replace(/<style>([\s\S]*?)<\/style>/gi, (match, css) => {
        extractedCss.push(css);
        // Replace ONLY the first style block with @vite, remove the others
        if (extractedCss.length === 1) {
            return "    @vite(['resources/css/app.css', 'resources/js/app.js'])";
        }
        return '';
    });
    
    // Si no había bloques style, asegurarnos de inyectar @vite antes de </head>
    if (extractedCss.length === 0 && !content.includes('@vite')) {
        content = content.replace('</head>', "    @vite(['resources/css/app.css', 'resources/js/app.js'])\n</head>");
    }

    fs.writeFileSync(filePath, content, 'utf8');
    return extractedCss.join('\n');
}

let allCss = "";
allCss += extractStyles(appBladePath);
allCss += "\n" + extractStyles(staffBladePath);

if (allCss.trim()) {
    let currentCss = fs.readFileSync(appCssPath, 'utf8');
    fs.writeFileSync(appCssPath, currentCss + '\n' + allCss, 'utf8');
    console.log("CSS Extraído correctamente en UTF-8");
} else {
    console.log("No se encontraron bloques de estilo.");
}
