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
        basepath: '/var/www/website.com/www/files/'

- **path:** Je relativní nebo absolutní cesta ke složce do které se mají vygenerované miniatury a obrázky ukládat. Tato složka musí existovat a musí být do ní možné zapisovat!
- **url:** Absolutní url adresa s lomítkem na konci na které je složka s miniatury veřejně dostupná.
- **basepath:** Absolutní cesta k document rootu webu. Tento parametr je nepovinný.

#### 4.  A na konec do routovacích pravidel aplikace přidat na první místo toto pravidlo:

    $router[] = new Route('files/thumbs/<path .+>', 'Palette:Palette:image');

Kde **files/thumbs/** je relativní veřejná url adresa ke složce miniatur.
Díky tomuto je možné aby Palette generovala miniatury obrázků vždy on demand.

## Použití v nette
V Nette je služba palette dostupná pod názvem **@palette.service**.

V Latte lze generovat miniatury a různé verze obrázků pomocí filtru palette na jehož vstupu musí být vždy cesta k souboru obrázku (ne url adresa) a palette query. Např.:

    <img src="{$image|palette:'Resize;100;150&Border;2;2;black'}" />

Tento kód vygeneruje z obrázku miniaturu obrázku o rozměrech 100 x 150px s 2px černým rámečkem okolo.
Seznam všech možných filtrů a effektů včetně používání samotného Palette naleznete na [Githubu Palette](https://github.com/MichaelPavlista/palette)

## Důležité odkazy
- [Github Palette](https://github.com/MichaelPavlista/palette)
- [Github Nette Palette](https://github.com/MichaelPavlista/nette-palette)
- [Dokumentace Palette a jejích filtrů](http://palette.pavlista.cz/)
