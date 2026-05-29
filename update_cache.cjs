const fs = require('fs');
const files = [
  'app/Http/Controllers/Api/BeritaController.php',
  'app/Http/Controllers/Api/DesaInfoApiController.php',
  'app/Http/Controllers/Api/StatisticApiController.php',
  'app/Http/Controllers/Api/TestimoniController.php'
];

files.forEach(f => {
  let text = fs.readFileSync(f, 'utf8');
  // Replace Cache::remember(..., TTL, ...) with Cache::remember(..., 15, ...)
  text = text.replace(/Cache::remember\(([^,]+),\s*\d+\s*,/g, 'Cache::remember($1, 15,');
  fs.writeFileSync(f, text);
});
