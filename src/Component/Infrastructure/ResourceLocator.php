<?php declare(strict_types=1);

namespace App\Component\Infrastructure;

use App\Entity\VO\Sex;

class ResourceLocator
{
    public const USER_AVATAR_DIR = 'build/static/img/avatar/';

    /**
     * @param Sex $sex
     *
     * @return string
     */
    public static function getDefaultAvatarForSex(Sex $sex): string
    {
        if ($sex->isMale()) {
            return self::USER_AVATAR_DIR . 'guy_default.png';
        }

        if ($sex->isFemale()) {
            return self::USER_AVATAR_DIR . 'girl_default.png';
        }

        return self::getDefaultAvatar();
    }

    /**
     * @return string
     */
    public static function getDefaultAvatar(): string
    {
        return self::USER_AVATAR_DIR . 'unknown_default.png';
    }
}
