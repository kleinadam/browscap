<?php
/**
 * This file is part of the browscap package.
 *
 * Copyright (c) 1998-2017, Browser Capabilities Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace Browscap\Data\Validator;

use Browscap\Data\Helper\CheckDeviceData;
use Browscap\Data\Helper\CheckEngineData;
use Browscap\Data\Helper\CheckPlatformData;

/**
 * Class Children
 *
 * @category   Browscap
 *
 * @author     James Titcumb <james@asgrim.com>
 */
class ChildrenData
{
    /**
     * @var \Browscap\Data\Helper\CheckDeviceData
     */
    private $checkDeviceData;

    /**
     * @var \Browscap\Data\Helper\CheckEngineData
     */
    private $checkEngineData;

    /**
     * @var \Browscap\Data\Helper\CheckPlatformData
     */
    private $checkPlatformData;

    public function __construct()
    {
        $this->checkDeviceData   = new CheckDeviceData();
        $this->checkEngineData   = new CheckEngineData();
        $this->checkPlatformData = new CheckPlatformData();
    }

    /**
     * @param array $childData
     * @param array $useragentData
     * @param array $versions
     *
     * @return void
     */
    public function validate(array $childData, array $useragentData, array $versions) : void
    {
        if (array_key_exists('device', $childData) && array_key_exists('devices', $childData)) {
            throw new \LogicException(
                'a child may not define both the "device" and the "devices" entries for key "'
                . $useragentData['userAgent'] . '", for child data: ' . json_encode($childData)
            );
        }

        if (array_key_exists('devices', $childData) && !is_array($childData['devices'])) {
            throw new \UnexpectedValueException(
                'the "devices" entry has to be an array for key "'
                . $useragentData['userAgent'] . '", for child data: ' . json_encode($childData)
            );
        }

        if (array_key_exists('device', $childData) && !is_string($childData['device'])) {
            throw new \UnexpectedValueException(
                'the "device" entry has to be a string for key "'
                . $useragentData['userAgent'] . '", for child data: ' . json_encode($childData)
            );
        }

        if (!array_key_exists('match', $childData)) {
            throw new \UnexpectedValueException(
                'each entry of the children property requires an "match" entry for key "'
                . $useragentData['userAgent'] . '", missing for child data: ' . json_encode($childData)
            );
        }

        if (!is_string($childData['match'])) {
            throw new \UnexpectedValueException(
                'the "match" entry for key "'
                . $useragentData['userAgent'] . '" has to be a string for child data: ' . json_encode($childData)
            );
        }

        if (preg_match('/[\[\]]/', $childData['match'])) {
            throw new \UnexpectedValueException(
                'key "' . $childData['match'] . '" includes invalid characters'
            );
        }

        if (false !== mb_strpos($childData['match'], '#PLATFORM#')
            && !array_key_exists('platforms', $childData)
        ) {
            throw new \UnexpectedValueException(
                'the key "' . $childData['match']
                . '" is defined with platform placeholder, but no platforms are assigned'
            );
        }

        if (array_key_exists('platforms', $childData)) {
            if (!is_array($childData['platforms'])) {
                throw new \UnexpectedValueException(
                    'the "platforms" entry for key "'
                    . $useragentData['userAgent'] . '" has to be an array for child data: ' . json_encode($childData)
                );
            }

            if (1 < count($childData['platforms'])
                && false === mb_strpos($childData['match'], '#PLATFORM#')
            ) {
                throw new \LogicException(
                    'the "platforms" entry contains multiple platforms but there is no #PLATFORM# token for key "'
                    . $useragentData['userAgent'] . '", for child data: ' . json_encode($childData)
                );
            }
        }

        if ((false !== mb_strpos($childData['match'], '#MAJORVER#')
                || false !== mb_strpos($childData['match'], '#MINORVER#'))
            && ['0.0'] === $versions
        ) {
            throw new \UnexpectedValueException(
                'the key "' . $childData['match']
                . '" is defined with version placeholders, but no versions are set'
            );
        }

        if (false === mb_strpos($childData['match'], '#MAJORVER#')
            && false === mb_strpos($childData['match'], '#MINORVER#')
            && ['0.0'] !== $versions
            && 1 < count($versions)
        ) {
            if (!array_key_exists('platforms', $childData)) {
                throw new \UnexpectedValueException(
                    'the key "' . $childData['match']
                    . '" is defined without version placeholders, but there are versions set'
                );
            }

            $dynamicPlatforms = false;

            foreach ($childData['platforms'] as $platform) {
                if (false !== mb_stripos($platform, 'dynamic')) {
                    $dynamicPlatforms = true;

                    break;
                }
            }

            if (!$dynamicPlatforms) {
                throw new \UnexpectedValueException(
                    'the key "' . $childData['match']
                    . '" is defined without version placeholders, but there are versions set'
                );
            }
        }

        if (false !== mb_strpos($childData['match'], '#DEVICE#')
            && !array_key_exists('devices', $childData)
        ) {
            throw new \UnexpectedValueException(
                'the key "' . $childData['match']
                . '" is defined with device placeholder, but no devices are assigned'
            );
        }

        if (array_key_exists('devices', $childData)) {
            if (!is_array($childData['devices'])) {
                throw new \UnexpectedValueException(
                    'the "devices" entry for key "'
                    . $useragentData['userAgent'] . '" has to be an array for child data: ' . json_encode($childData)
                );
            }

            if (1 < count($childData['devices'])
                && false === mb_strpos($childData['match'], '#DEVICE#')
            ) {
                throw new \LogicException(
                    'the "devices" entry contains multiple devices but there is no #DEVICE# token for key "'
                    . $useragentData['userAgent'] . '", for child data: ' . json_encode($childData)
                );
            }
        }

        if (array_key_exists('properties', $childData)) {
            if (!is_array($childData['properties'])) {
                throw new \UnexpectedValueException(
                    'the properties entry has to be an array for key "' . $childData['match'] . '"'
                );
            }

            if (array_key_exists('Parent', $childData['properties'])) {
                throw new \UnexpectedValueException(
                    'the Parent property must not set inside the children array for key "'
                    . $childData['match'] . '"'
                );
            }

            if (array_key_exists('Version', $childData['properties'])
                && array_key_exists('Version', $useragentData['properties'])
                && $useragentData['properties']['Version'] === $childData['properties']['Version']
            ) {
                throw new \UnexpectedValueException(
                    'the "Version" property is set for key "' . $childData['match']
                    . '", but was already set for its parent "' . $useragentData['userAgent'] . '"'
                );
            }

            $this->checkPlatformData->check(
                $childData['properties'],
                'the properties array contains platform data for key "' . $childData['match']
                . '", please use the "platforms" keyword'
            );

            $this->checkEngineData->check(
                $childData['properties'],
                'the properties array contains engine data for key "' . $childData['match']
                . '", please use the "engine" keyword'
            );

            $this->checkDeviceData->check(
                $childData['properties'],
                'the properties array contains device data for key "' . $childData['match']
                . '", please use the "device" or the "devices" keyword'
            );
        }
    }
}
