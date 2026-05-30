const fs = require('fs');
const path = require('path');

const files = [
    'resources/views/diseños/app.blade.php',
    'resources/views/diseños/staff.blade.php',
    'resources/views/auth/login.blade.php',
    'resources/views/registro/registro.blade.php',
    'resources/views/registro/verificar.blade.php'
];

const map = {
    'Generacin': 'Generación',
    'Atencin!': '¡Atención!',
    'Ests': '¿Estás',
    'S,': 'SÍ,',
    'Sesin': 'Sesión',
    'Configuracin': 'Configuración',
    'Administracin': 'Administración',
    'Aadir': 'Añadir',
    'Diseos': 'Diseños',
    'electrnico': 'electrónico',
    'Contrasea': 'Contraseña',
    'Mvil': 'Móvil',
    'aqu': 'aquí',
    'Nio': 'Niño',
    'Nios': 'Niños',
    '': 'á', // un poco riesgoso, veamos si hay otros.
};

files.forEach(file => {
    let p = path.join(__dirname, file);
    if (!fs.existsSync(p)) return;
    
    let content = fs.readFileSync(p, 'utf8');
    
    // reemplazos seguros
    content = content.replace(/Generacin/g, 'Generación');
    content = content.replace(/Atencin!/g, '¡Atención!');
    content = content.replace(/Ests/g, '¿Estás');
    content = content.replace(/S,/g, 'SÍ,');
    content = content.replace(/Sesin/g, 'Sesión');
    content = content.replace(/Configuracin/g, 'Configuración');
    content = content.replace(/Administracin/g, 'Administración');
    content = content.replace(/Diseos/g, 'Diseños');
    content = content.replace(/Contrasea/g, 'Contraseña');
    content = content.replace(/electrnico/g, 'electrónico');
    content = content.replace(/Mvil/g, 'Móvil');
    content = content.replace(/aqu/g, 'aquí');
    content = content.replace(/Nio/g, 'Niño');
    content = content.replace(/Nios/g, 'Niños');
    content = content.replace(/Aadir/g, 'Añadir');
    content = content.replace(/Aos/g, 'Años');
    content = content.replace(/aos/g, 'años');
    content = content.replace(/tamao/g, 'tamaño');
    content = content.replace(/Tambin/g, 'También');
    content = content.replace(/tambin/g, 'también');
    content = content.replace(/Accin/g, 'Acción');
    content = content.replace(/accin/g, 'acción');
    content = content.replace(/Informacin/g, 'Información');
    content = content.replace(/informacin/g, 'información');
    content = content.replace(/Trminos/g, 'Términos');
    content = content.replace(/ /g, '¿ '); // abrir pregunta
    
    // escribir en utf8
    fs.writeFileSync(p, content, 'utf8');
    console.log(`Arreglado: ${file}`);
});
