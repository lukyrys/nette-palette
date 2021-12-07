# Nette Palette
Nette palette je rozšíření pro Nette Framework které umožňuje jednoduchuché i pokročilé úpravy obrazových souborů včetně inteligentního generování miniatur a náhledů.

Palette u obrázků například umožňuje: změny rozměrů, vkládání vodoznaků, pokročilé transformace, nastavení průhlednosti a množství dalších efektních filtrů a funkcí.

## Instalace a integrace do Nette
#### 1. Nejdříve Palette naistalujeme do projektu nejlépe pomocí [composeru](https://getcomposer.org/).

    php composer.phar require pavlista/nette-palette

#### 2. Po té v Nette do config.neon zaregistrujeme rozšíření.

    extensions:
        palette: NettePalette\PaletteExtension

#### 3. Do config.neon také přidáme sekci s nastavením rozšíření a správně ji vyplníme.

    palette:
        path: 'files/thumbs/'
        url: 'http://website.com/files/thumbs/'
        signingKey: '%uniqueSigningKey%'
        basepath: '/var/www/website.com/www/files/'        

- **path:** Je relativní nebo absolutní cesta ke složce do které se mají vygenerované miniatury a obrázky ukládat. Tato složka musí existovat a musí být do ní možné zapisovat!
- **url:** Absolutní url adresa s lomítkem na konci na které je složka s miniatury veřejně dostupná.
- **signingKey:** Náhodný řetězec, kterým se podepisují (http) požadavky na generování miniatur.
- **basepath:** *(nepovinný)* Absolutní cesta k document rootu webu.
- **fallbackImage:** *(nepovinný)* Absolutní cesta k obrázku, který se použije v případě, že požadovaný obrázek neexistuje (užitečné hlavně pro DEV).
- **template:** *(nepovinný)* Pole šablon ve tvaru `název šablony` => `paletteQuery`. Šablony je možné používat v palette query přes `.` např.: `.template`.
- **websiteUrl:** *(nepovinný)* Adresa aplikace s lomítkem na konci pro generování absolutních url adres k obrázkům v cli (např.: `https://localhost/`).
- **pictureLoader:** *(nepovinný)* Služba implementující interface `IPictureLoader`, přes kterou je možné upravit logiku načítání a generování obrázků přes Palette.
- **handleException:** *(nepovinný)* Jak se má pracovat s výjímkami při generování obrázků? true *(default)* - výjímky se logují přes tracy, false - výjímky se vyhazují, string - výjímky se logují do souboru přes tracy.
- **webpMacroDefaultQuality:** *(nepovinný)* Definice výchozí kvality (1–100) WebP obrázků pro makro n:webp. Pokud není zadáno, použije se `100`.

#### 4.  A na konec do routovacích pravidel aplikace přidat na první místo toto pravidlo:

    $router[] = new Route('files/thumbs/<path .+>', 'Palette:Palette:image');

Kde **files/thumbs/** je relativní veřejná url adresa ke složce miniatur.
Díky tomuto je možné aby Palette generovala miniatury obrázků vždy on demand.

## Použití v nette
V Nette je služba palette dostupná pod názvem **@palette.service**.

### Latte
V Latte je možné palette používat následujícími způsoby:
_________________

#### Makro n:palette-src
Makro je možné použít u HTML tagu `<img>` a `<source>`.  
Na vstupu musí být vždy cesta k souboru obrázku (ne url adresa) a palette query.

**Příklad:**
```latte
{* Použití v tagu img (src) *}
<img n:palette-src="$image, '300;300;fit'" alt="Image" />

{* Použití v tagu source (srcset) *}
<source n:palette-src="$image, '300;300;fit&Grayscale&WebP'" type="image/webP">
```

**Výsledné HTML:**
```html
<img alt="Image" src="/demo/www/nette3.0/thumbs/images/portrait.4087713685.1638810813.jpg">
<source type="image/webP" srcset="/demo/www/nette3.0/thumbs/images/portrait.2202428379.1638810813.jpg.webp">
```
_________________

#### Filtr palette
V Latte lze generovat miniatury a různé verze obrázků pomocí filtru palette 
na jehož vstupu musí být vždy cesta k souboru obrázku (ne url adresa) a palette query.

**Příklad:**
```latte
<img src="{$image|palette:'Resize;100;150&Border;2;2;black'}" />
```
Tento kód vygeneruje z obrázku miniaturu obrázku o rozměrech 100 x 150px s 2px černým rámečkem okolo.
_________________

#### Makro n:webp
Pomocí tohoto makra je možné vygenerovat picture set obrázků, kde jako první verze bude 
verze obrázu ve formátu WebP s fallbackem na původní formát souboru (jpg|png|gif).
Makro je možné použít pouze HTML tagu `<picture>`.  

Uvnitř tagu picture musí být zadán jeden tag `img` s definicí zdrojového obrázku setu
přes makro `n:picture-src`  
(argumenty tohoto makra jsou shodné s makrem n:palette-src).  

V makru je možné zadat procentuální kvalitu výsledného obrázku (1-100), 
pokud není zadána použije se hodnota z konfigurace `webpMacroDefaultQuality`.

**Příklady:**
```latte
<div>JPG</div>
<picture n:webp>
    <img n:picture-src="$imageJpg, '300;300;fit'" alt="Image" />
</picture>

<div>JPG - Custom WebP quality (1–100)</div>
<picture n:webp="10">
    <img n:picture-src="$imageJpg, '300;300;fit'" alt="Image" />
</picture>

<div>WebP</div>
<picture n:webp>
    <img n:picture-src="$imageWebp, '300;300;fit'" alt="Image" />
</picture>
```

**Výsledek:**
```html
<div>JPG</div>
<picture>
    <source srcset="/images/image.3082875972.1638810813.jpg.webp" type="image/webp">
    <source srcset="/images/image.2641037063.1638810813.jpg" type="image/jpeg">
    <img alt="Image" src="/images/image.2641037063.1638810813.jpg">
</picture>

<div>JPG - Custom WebP quality (1–100)</div>
<picture>
    <source srcset="/images/image.1770219929.1638810813.jpg.webp" type="image/webp">
    <source srcset="/images/image.2641037063.1638810813.jpg" type="image/jpeg">
    <img alt="Image" src="/images/image.2641037063.1638810813.jpg">
</picture>

<div>WebP</div>
<picture>
    <source srcset="/images/image.6612593.1638810813.webp" type="image/webp">
    <img alt="Image" src="/images/image.6612593.1638810813.webp">
</picture>
```
_________________
Seznam všech možných filtrů a effektů včetně používání samotného Palette naleznete na [Githubu Palette](https://github.com/MichaelPavlista/palette)

## Důležité odkazy
- [Github Palette](https://github.com/MichaelPavlista/palette)
- [Github Nette Palette](https://github.com/MichaelPavlista/nette-palette)
- [Dokumentace Palette a jejích filtrů](http://palette.pavlista.cz/)
