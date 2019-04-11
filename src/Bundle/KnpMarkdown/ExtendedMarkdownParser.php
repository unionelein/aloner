<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Bundle\KnpMarkdown;

use Knp\Bundle\MarkdownBundle\Parser\MarkdownParser;

class ExtendedMarkdownParser extends MarkdownParser
{
    public function transformMarkdown($text)
    {
        $transformed = parent::transformMarkdown($text);

        $result = \str_replace('%tab%', '&nbsp;&nbsp;&nbsp;&nbsp;', $transformed);

        return $result;
    }
}
