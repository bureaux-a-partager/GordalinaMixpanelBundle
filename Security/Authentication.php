<?php

/*
 * This file is part of the mixpanel bundle.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/mixpanel-bundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\MixpanelBundle\Security;

use Gordalina\MixpanelBundle\ManagerRegistry;
use Gordalina\MixpanelBundle\Mixpanel\Flusher;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Authentication
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var UserData
     */
    private $userData;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param UserData        $userData
     */
    public function __construct(ManagerRegistry $registry, UserData $userData, Flusher $flusher)
    {
        $this->registry = $registry;
        $this->userData = $userData;
        $this->flusher  = $flusher;
    }

    /**
     * @param  TokenInterface $token
     * @return boolean
     */
    public function onAuthenticationSuccess(TokenInterface $token)
    {
        if (null === ($user = $token->getUser())) {
            return null;
        }

        $id = $this->userData->getId($user);

        foreach ($this->registry->getProjects() as $project) {
            $project->identify($id);
        }

        return true;
    }

    /**
     * @return null
     */
    public function onAuthenticationFailure()
    {
        $this->flusher->flush();

        foreach ($this->registry->getProjects() as $project) {
            // clear identity
            $project->identify(null);
            $project->unregister('distinct_id');
        }
    }
}
