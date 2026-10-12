# Bilder

Bara bilder som ägaren har godkänt för publicering får användas (CONTENT-09, B07). Hero-bilden och galleribilderna 01–05 från prototypen godkändes 2026-10-11. Råmaterialet i `public/assets/photos/` är gitignorerat och publiceras inte.

Varje bild registreras i `app/Config/Images.php` med JPEG-reserv, WebP-varianter och en saklig alt-text. Vyerna använder `picture('nyckel', sizes, priority)`.

## Lägga till eller byta en bild

```bash
# Kräver cwebp (brew install webp). Exempel för en galleribild:
sips -Z 1024 original.jpg --out public/assets/gallery/06-ny.jpg
for w in 640 1024; do
  sips -Z $w original.jpg --out /tmp/ny-$w.jpg
  cwebp -q 74 /tmp/ny-$w.jpg -o public/assets/img/06-ny-$w.webp
done
```

Lägg sedan till bilden i `Config\Images::$images` och, om den ska synas i galleriet, i `$gallery`.

Hero-bilden finns i 800, 1280 och 1920 px och förladdas på startsidan (LCP). Övriga bilder laddas med `loading="lazy"`.
