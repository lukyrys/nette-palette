<?php declare(strict_types=1);

namespace NettePalette\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Validators;

/**
 * LatteMacroSet
 * @package NettePalette\Latte
 * @url https://usefulangle.com/post/114/webp-image-in-html-with-fallback
 */
final class LatteMacroSet extends MacroSet
{
    /** @var bool je aktuálně vykreslován tag picture přes makro? */
    private $isInPicture = false;

    /** @var string|null PHP kód pro načtení zdrojového obrázku pro picture set. */
    private $pictureSrcPhpCode = null;

    /** @var int<1, 100>|null přetížení výchozí kvality WebP obrázků. */
    private $macroWebPQuality = null;


    /**
     * Provede instalaci palette maker do Latte compileru.
     * @param Compiler $compiler
     * @return void
     */
    public static function install(Compiler $compiler): void
    {
        $me = new LatteMacroSet($compiler);

        // Přidání makra pro vygenerování picture setu s WebP verzí obrázku.
        $me->addMacro('webp', [$me, 'macroWebpOpen'], [$me, 'macroWebpClose']);

        // Přidání makra pro definici zdrojového obrázku picture setu (tento obrázek se používá jako fallback pro staré prohlížeče).
        $me->addMacro('picture-src', null, null, [$me, 'macroPictureSrc']);

        // Přidání makra pro vygenerování src odkazu pro obrázek přes n:palette-src.
        $me->addMacro('palette-src', null, null, [$me, 'macroPaletteSrc']);
    }


    /**
     * Provedení akcí při začátku vykreslování picture setu.
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroWebpOpen(MacroNode $node, PhpWriter $writer): string
    {
        $this->isInPicture = true;
        $this->pictureSrcPhpCode = null;
        $this->macroWebPQuality = null;

        // Makro n:webp je možné používat pouze na html tagu picture.
        if (!$node->htmlNode || strtolower($node->htmlNode->name) !== 'picture')
        {
            throw new CompileException('Macro n:webp is allowed only on html tag <picture> in ' . $node->getNotation());
        }

        // Načteme a zvalidujeme vlastní definici kvality WebP obrázků v makru.
        $quality = $node->tokenizer->fetchWord();

        if ($quality !== null)
        {
            $intQuality = (int) $quality;

            if ($intQuality <= 0 || $intQuality > 100 || !Validators::isNumericInt($quality))
            {
                throw new CompileException('Quality of must be int<1, 100>|null in ' . $node->getNotation());
            }

            $this->macroWebPQuality = $intQuality;
        }

        return '';
    }


    /**
     * Provedení akcí při dokončení vykreslování picture setu.
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroWebpClose(MacroNode $node, PhpWriter $writer): string
    {
        $this->isInPicture = false;

        if (!$this->pictureSrcPhpCode)
        {
            throw new CompileException('Missing picture-src inside macro webp in ' . $node->getNotation());
        }

        // Vygenerování source setů přes palette.
        // (kód v picture tagu je nutné striktně seřadit!)
        $node->innerContent =
            // Sestavení instance zdrojového obrázku.
            $this->pictureSrcPhpCode .
            // Vygenerované srcsety
            $writer->write(
                '<?php echo NettePalette\Latte\LatteHelpers::generatePictureSrcSetHtml(%var, $this->global->palette, $__paletteSourcePicture); ?>',
                $this->macroWebPQuality
            ) .
            // Fallback tag img a případný existující obsah tagu <picture>
            $node->innerContent .
            // Promazání dočasných proměnných.
            $writer->write('<?php unset($__paletteSourcePicture); ?>');

        return '';
    }


    /**
     * Definice zdrojového obrázku pro picture set, tento obrázek se použije jako fallback.
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroPictureSrc(MacroNode $node, PhpWriter $writer): string
    {
        // Makro `picture-src` je možné používat pouze uvnitř makra `webp`.
        if (!$this->isInPicture)
        {
            throw new CompileException('Macro picture-src is only available inside macro webp in ' . $node->getNotation());
        }

        // Je možné definovat pouze jeden zdrojový obrázek pro picture set.
        if ($this->pictureSrcPhpCode !== null)
        {
            throw new CompileException('Multiple picture-src in webp macro is forbidden in ' . $node->getNotation());
        }

        // Makro n:picture-src je možné používat pouze na html tagu img.
        if (!$node->htmlNode || strtolower($node->htmlNode->name) !== 'img')
        {
            throw new CompileException('Macro n:picture-src is allowed only on html tag <img> in ' . $node->getNotation());
        }

        // Vygenerujeme PHP kód pro načtení zdrojového obrázku setu přes Palette.
        $this->pictureSrcPhpCode = $writer->write(
            '<?php $__paletteSourcePicture=$this->global->palette->getSourcePicture(true, %var, %node.args); ?>',
            $this->macroWebPQuality
        );

        // Vygenerujeme PHP/HTML kód img tagu s fallbackem picture setu.
        return
            ' ?> src="<?php ' .
            $writer->write('echo NettePalette\Latte\LatteHelpers::generateImgUrl($this->global->palette, $__paletteSourcePicture); ') .
            '?>"<?php ';
    }


    /**
     * Definice zdrojového obrázku img tagu přes makro n:palette.
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroPaletteSrc(MacroNode $node, PhpWriter $writer): string
    {
        $htmlNodeName = $node->htmlNode ? strtolower($node->htmlNode->name) : null;

        // Makro n:palette-src je možné používat pouze na html tagu img | source.
        if (!in_array($htmlNodeName, ['img', 'source'], true))
        {
            throw new CompileException('Macro n:picture-src is allowed only on html tag <img> or <source> in ' . $node->getNotation());
        }

        // Vygenerujeme název html atributu s cestou k obrázku.
        $htmlSrcAttribute = $htmlNodeName === 'source' ? 'srcset' : 'src';

        return
            ' ?> ' . $htmlSrcAttribute . '="<?php ' .
            $writer->write('echo $this->global->palette->getSourcePicture(false, null, %node.args)->getPictureUrl(); ') .
            '?>"<?php ';
    }
}
