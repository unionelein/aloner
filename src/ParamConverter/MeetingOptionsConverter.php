<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\ParamConverter;

use App\Entity\VO\MeetingOptions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class MeetingOptionsConverter implements ParamConverterInterface
{
    private const MEETING_AT_NAME = 'meetingAt';

    private const MEETING_PLACE_NAME = 'meetingPlace';

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $meetingAt    = $request->get(self::MEETING_AT_NAME);
        $meetingPlace = $request->get(self::MEETING_PLACE_NAME);

        if (!$meetingAt || !$meetingPlace) {
            return false;
        }

        $MO = new MeetingOptions(new \DateTime($meetingAt), $meetingPlace);

        $request->attributes->set($configuration->getName(), $MO);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return MeetingOptions::class === $configuration->getClass();
    }
}
